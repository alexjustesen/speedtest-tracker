<?php

namespace App\Notifications\Apprise;

class AppriseMessage
{
    public function __construct(
        public string|array $urls,
        public string $title,
        public string $body,
        public string $type = 'info',
        public string $format = 'text',
        public ?string $tag = null,
    ) {}

    public static function create(): self
    {
        return new self(
            urls: '',
            title: '',
            body: '',
        );
    }

    public function urls(string|array $urls): self
    {
        $this->urls = $urls;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function tag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
