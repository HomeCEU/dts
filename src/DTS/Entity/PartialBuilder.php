<?php

namespace HomeCEU\DTS\Entity;

class PartialBuilder {
  private string $id;
  private string $name;
  private string $docType;
  private string $body;
  private string $author = '';
  private array $meta = [];
  private \DateTime $createdAt;

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

  public function withMeta(array $meta): self {
    $this->meta = $meta;
    return $this;
  }

  public static function create(): self {
    $p = new self();
    $p->id = Id::create();
    $p->createdAt = new \DateTime();
    return $p;
  }

  public function build(): Partial {
    return Partial::fromState([
        'id' => $this->id,
        'docType' => $this->docType,
        'name' => $this->name,
        'author' => $this->author,
        'body' => $this->body,
        'meta' => $this->meta,
        'createdAt' => $this->createdAt
    ]);
  }
}
