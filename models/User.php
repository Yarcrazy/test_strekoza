<?php

declare(strict_types=1);

namespace app\models;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%users}}';
    }

    public function rules(): array
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['username'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['created_at'], 'safe'],
        ];
    }

    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        $jwtSecret = $_ENV['JWT_SECRET'] ?? throw new \RuntimeException('JWT_SECRET not set in .env');
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($jwtSecret)
        );

        try {
            $parsedToken = $config->parser()->parse($token);
            $constraints = [
                new SignedWith($config->signer(), $config->signingKey()),
                new LooseValidAt(new SystemClock(new \DateTimeZone('UTC'))),
            ];
            if ($config->validator()->validate($parsedToken, ...$constraints)) {
                $uid = $parsedToken->claims()->get('uid');
                return static::findOne($uid);
            }
        } catch (\Exception $e) {
            \Yii::error('Invalid JWT: ' . $e->getMessage(), __METHOD__);
            throw new UnauthorizedHttpException('Invalid or expired token.');
        }

        return null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return null;
    }

    public function validateAuthKey($authKey): bool
    {
        return false;
    }

    public function validatePassword(string $password): bool
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    public function generateJwtToken(): string
    {
        $jwtSecret = $_ENV['JWT_SECRET'] ?: throw new \RuntimeException('JWT_SECRET not set in .env');
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($jwtSecret)
        );

        $now = new \DateTimeImmutable();
        return $config->builder()
            ->issuedBy('your-app')
            ->permittedFor('your-app')
            ->identifiedBy(\Yii::$app->security->generateRandomString())
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $this->id)
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
}