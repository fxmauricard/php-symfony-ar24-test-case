<?php

namespace App\Tests\Infrastructure\Ar24\Http\Common\DataTransformer;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute\JsonField;
use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

// Test enums for testing enum support
enum TestUserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
}

enum TestPriority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
}

final class AutomaticTransformerTest extends TestCase
{
    private AutomaticTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new AutomaticTransformer();
    }

    public function testTransformWithSimpleTypes(): void
    {
        $user = new class {
            #[JsonField]
            private ?int $id = null;

            #[JsonField]
            private ?string $name = null;

            #[JsonField]
            private ?bool $active = null;

            public function __construct()
            {
                $this->id = 123;
                $this->name = 'John';
                $this->active = true;
            }

            public function getId(): ?int { return $this->id; }
            public function getName(): ?string { return $this->name; }
            public function isActive(): ?bool { return $this->active; }
        };

        $result = $this->transformer->transform($user);

        $this->assertSame(123, $result['id']);
        $this->assertSame('John', $result['name']);
        $this->assertSame(1, $result['active']); // bool converti en int
    }

    public function testTransformWithDateTimeImmutable(): void
    {
        $model = new class {
            #[JsonField]
            private ?DateTimeImmutable $createdAt = null;

            public function __construct()
            {
                $this->createdAt = new DateTimeImmutable('2024-01-15 10:30:45');
            }

            public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
        };

        $result = $this->transformer->transform($model);

        $this->assertSame('2024-01-15 10:30:45', $result['createdAt']);
    }

    public function testTransformWithCustomJsonName(): void
    {
        $user = new class {
            #[JsonField(name: 'user_email')]
            private ?string $email = null;

            public function __construct()
            {
                $this->email = 'john@example.com';
            }

            public function getEmail(): ?string { return $this->email; }
        };

        $result = $this->transformer->transform($user);

        $this->assertArrayHasKey('user_email', $result);
        $this->assertSame('john@example.com', $result['user_email']);
    }

    public function testTransformSkipNullFields(): void
    {
        $user = new class {
            #[JsonField]
            private ?string $name = null;

            #[JsonField(skipNull: true)]
            private ?string $email = null;

            public function __construct()
            {
                $this->name = 'John';
                $this->email = null;
            }

            public function getName(): ?string { return $this->name; }
            public function getEmail(): ?string { return $this->email; }
        };

        $result = $this->transformer->transform($user);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayNotHasKey('email', $result);
    }

    public function testReverseTransformWithSimpleTypes(): void
    {
        $className = new class {
            #[JsonField]
            private ?int $id = null;

            #[JsonField]
            private ?string $name = null;

            #[JsonField]
            private ?bool $active = null;

            public function setId(?int $id): self { $this->id = $id; return $this; }
            public function getId(): ?int { return $this->id; }
            public function setName(?string $name): self { $this->name = $name; return $this; }
            public function getName(): ?string { return $this->name; }
            public function setActive(?bool $active): self { $this->active = $active; return $this; }
            public function isActive(): ?bool { return $this->active; }
        };

        $data = [
            'id' => '456',
            'name' => 'Jane',
            'active' => 1,
        ];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertSame(456, $object->getId());
        $this->assertSame('Jane', $object->getName());
        $this->assertTrue($object->isActive());
    }

    public function testReverseTransformWithDateTimeImmutable(): void
    {
        $className = new class {
            #[JsonField]
            private ?DateTimeImmutable $createdAt = null;

            public function setCreatedAt(?DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
            public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
        };

        $data = [
            'createdAt' => '2024-06-20 14:15:30',
        ];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertInstanceOf(DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertSame('2024-06-20 14:15:30', $object->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    public function testReverseTransformWithCustomDateFormat(): void
    {
        $className = new class {
            #[JsonField(dateFormat: 'Y-m-d')]
            private ?DateTimeImmutable $date = null;

            public function setDate(?DateTimeImmutable $date): self { $this->date = $date; return $this; }
            public function getDate(): ?DateTimeImmutable { return $this->date; }
        };

        $data = [
            'date' => '2024-06-20',
        ];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertSame('2024-06-20', $object->getDate()->format('Y-m-d'));
    }

    public function testRoundTripTransformation(): void
    {
        $className = new class {
            #[JsonField]
            private ?int $id = null;

            #[JsonField]
            private ?string $name = null;

            #[JsonField]
            private ?bool $active = null;

            #[JsonField]
            private ?DateTimeImmutable $createdAt = null;

            public function setId(?int $id): self { $this->id = $id; return $this; }
            public function getId(): ?int { return $this->id; }
            public function setName(?string $name): self { $this->name = $name; return $this; }
            public function getName(): ?string { return $this->name; }
            public function setActive(?bool $active): self { $this->active = $active; return $this; }
            public function isActive(): ?bool { return $this->active; }
            public function setCreatedAt(?DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
            public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
        };

        $original = $this->transformer->reverseTransform([
            'id' => '789',
            'name' => 'Test User',
            'active' => 1,
            'createdAt' => '2024-01-15 10:30:45',
        ], $className::class);

        $transformed = $this->transformer->transform($original);

        $this->assertSame(789, $transformed['id']);
        $this->assertSame('Test User', $transformed['name']);
        $this->assertSame(1, $transformed['active']);
        $this->assertSame('2024-01-15 10:30:45', $transformed['createdAt']);
    }

    public function testIgnoresPropertiesWithoutAttribute(): void
    {
        $user = new class {
            #[JsonField]
            private ?string $name = null;

            private ?string $secret = null; // Pas d'attribut

            public function __construct()
            {
                $this->name = 'Public';
                $this->secret = 'Hidden';
            }

            public function getName(): ?string { return $this->name; }
            public function getSecret(): ?string { return $this->secret; }
        };

        $result = $this->transformer->transform($user);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayNotHasKey('secret', $result);
    }

    public function testTransformWithStringBackedEnum(): void
    {
        $model = new class {
            #[JsonField]
            private ?TestUserStatus $status = null;

            public function __construct()
            {
                $this->status = TestUserStatus::ACTIVE;
            }

            public function getStatus(): ?TestUserStatus { return $this->status; }
        };

        $result = $this->transformer->transform($model);

        $this->assertSame('active', $result['status']);
    }

    public function testTransformWithIntBackedEnum(): void
    {
        $model = new class {
            #[JsonField]
            private ?TestPriority $priority = null;

            public function __construct()
            {
                $this->priority = TestPriority::HIGH;
            }

            public function getPriority(): ?TestPriority { return $this->priority; }
        };

        $result = $this->transformer->transform($model);

        $this->assertSame(3, $result['priority']);
    }

    public function testTransformWithNullEnum(): void
    {
        $model = new class {
            #[JsonField]
            private ?TestUserStatus $status = null;

            public function getStatus(): ?TestUserStatus { return $this->status; }
        };

        $result = $this->transformer->transform($model);

        $this->assertArrayHasKey('status', $result);
        $this->assertNull($result['status']);
    }

    public function testReverseTransformWithStringBackedEnum(): void
    {
        $className = new class {
            #[JsonField]
            private ?TestUserStatus $status = null;

            public function setStatus(?TestUserStatus $status): self { $this->status = $status; return $this; }
            public function getStatus(): ?TestUserStatus { return $this->status; }
        };

        $data = ['status' => 'active'];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertInstanceOf(TestUserStatus::class, $object->getStatus());
        $this->assertSame(TestUserStatus::ACTIVE, $object->getStatus());
        $this->assertSame('active', $object->getStatus()->value);
    }

    public function testReverseTransformWithIntBackedEnum(): void
    {
        $className = new class {
            #[JsonField]
            private ?TestPriority $priority = null;

            public function setPriority(?TestPriority $priority): self { $this->priority = $priority; return $this; }
            public function getPriority(): ?TestPriority { return $this->priority; }
        };

        $data = ['priority' => 2];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertInstanceOf(TestPriority::class, $object->getPriority());
        $this->assertSame(TestPriority::MEDIUM, $object->getPriority());
        $this->assertSame(2, $object->getPriority()->value);
    }

    public function testReverseTransformWithInvalidEnumValue(): void
    {
        $className = new class {
            #[JsonField]
            private ?TestUserStatus $status = null;

            public function setStatus(?TestUserStatus $status): self { $this->status = $status; return $this; }
            public function getStatus(): ?TestUserStatus { return $this->status; }
        };

        $data = ['status' => 'invalid_value'];

        $object = $this->transformer->reverseTransform($data, $className::class);

        // tryFrom() returns null for invalid values
        $this->assertNull($object->getStatus());
    }

    public function testRoundTripTransformationWithEnum(): void
    {
        $className = new class {
            #[JsonField]
            private ?int $id = null;

            #[JsonField]
            private ?TestUserStatus $status = null;

            #[JsonField]
            private ?TestPriority $priority = null;

            public function setId(?int $id): self { $this->id = $id; return $this; }
            public function getId(): ?int { return $this->id; }
            public function setStatus(?TestUserStatus $status): self { $this->status = $status; return $this; }
            public function getStatus(): ?TestUserStatus { return $this->status; }
            public function setPriority(?TestPriority $priority): self { $this->priority = $priority; return $this; }
            public function getPriority(): ?TestPriority { return $this->priority; }
        };

        // Create object from array
        $original = $this->transformer->reverseTransform([
            'id' => '999',
            'status' => 'pending',
            'priority' => 1,
        ], $className::class);

        // Verify enums are properly created
        $this->assertSame(TestUserStatus::PENDING, $original->getStatus());
        $this->assertSame(TestPriority::LOW, $original->getPriority());

        // Transform back to array
        $transformed = $this->transformer->transform($original);

        // Verify enum values are properly extracted
        $this->assertSame(999, $transformed['id']);
        $this->assertSame('pending', $transformed['status']);
        $this->assertSame(1, $transformed['priority']);
    }

    public function testTransformWithPublicProperties(): void
    {
        $model = new class {
            #[JsonField]
            public ?int $id = 123;

            #[JsonField(name: 'user_name')]
            public ?string $name = 'Alice';

            public ?string $notTransformed = 'secret'; // No attribute

            public function getId(): ?int { return $this->id; }
            public function getName(): ?string { return $this->name; }
            public function getNotTransformed(): ?string { return $this->notTransformed; }
        };

        $result = $this->transformer->transform($model);

        $this->assertSame(123, $result['id']);
        $this->assertSame('Alice', $result['user_name']);
        $this->assertArrayNotHasKey('notTransformed', $result);
    }

    public function testReverseTransformWithPublicProperties(): void
    {
        $className = new class {
            #[JsonField]
            public ?int $id = null;

            #[JsonField(name: 'user_name')]
            public ?string $name = null;

            public function setId(?int $id): self { $this->id = $id; return $this; }
            public function getId(): ?int { return $this->id; }
            public function setName(?string $name): self { $this->name = $name; return $this; }
            public function getName(): ?string { return $this->name; }
        };

        $data = ['id' => '999', 'user_name' => 'Bob'];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertSame(999, $object->getId());
        $this->assertSame('Bob', $object->getName());
    }

    public function testTransformWithMixedVisibility(): void
    {
        $model = new class {
            #[JsonField]
            private ?int $privateId = 1;

            #[JsonField]
            protected ?string $protectedName = 'Protected';

            #[JsonField]
            public ?bool $publicActive = true;

            public function getPrivateId(): ?int { return $this->privateId; }
            public function getProtectedName(): ?string { return $this->protectedName; }
            public function isPublicActive(): ?bool { return $this->publicActive; }
        };

        $result = $this->transformer->transform($model);

        $this->assertSame(1, $result['privateId']);
        $this->assertSame('Protected', $result['protectedName']);
        $this->assertSame(1, $result['publicActive']); // bool -> int
        $this->assertCount(3, $result);
    }

    public function testTransformWithoutGetters(): void
    {
        $model = new class {
            #[JsonField]
            private ?int $id = 42;

            #[JsonField]
            private ?string $name = 'NoGetters';

            // No getters defined - transformer accesses properties directly
        };

        $result = $this->transformer->transform($model);

        $this->assertSame(42, $result['id']);
        $this->assertSame('NoGetters', $result['name']);
    }

    public function testReverseTransformWithoutSetters(): void
    {
        $className = new class {
            #[JsonField]
            private ?int $id = null;

            #[JsonField]
            private ?string $name = null;

            // No setters defined - transformer sets properties directly

            // Getters only needed for assertions
            public function getId(): ?int { return $this->id; }
            public function getName(): ?string { return $this->name; }
        };

        $data = ['id' => '123', 'name' => 'DirectSet'];

        $object = $this->transformer->reverseTransform($data, $className::class);

        $this->assertSame(123, $object->getId());
        $this->assertSame('DirectSet', $object->getName());
    }
}

