<?php

namespace HomeCEU\DTS\Entity;

class PartialBuilder {
  private string $id;
  private string $name;
  private string $docType;
  private string $body;
  private string $author = '';
  private array $metadata = [];
  private \DateTime $createdAt;

  private function __construct(string $id, \DateTime $createdAt) {
    $this->id = $id;
    $this->createdAt = $createdAt;
  }

  public static function create(): self {
    return new self(IdGenerator::create(), new \DateTime());
  }

  public function withName(string $name): self {
    $this->name = $name;
    return $this;
  }

  public function withDocType(string $docType): self {
    $this->docType = $docType;
    return $this;
  }

  public function withBody(string $body): self {
    $this->body = $body;
    return $this;
  }

  public function withAuthor(string $author): self {
    $this->author = $author;
    return $this;
  }

  public function withMetadata(array $metadata): self {
    $this->metadata = $metadata;
    return $this;
  }

  public function build(): Partial {
    return Partial::fromState([
        'id' => $this->id,
        'docType' => $this->docType,
        'name' => $this->name,
        'author' => $this->author,
        'body' => $this->body,
        'metadata' => $this->metadata,
        'createdAt' => $this->createdAt
    ]);
  }
}
