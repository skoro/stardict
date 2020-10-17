<?php declare(strict_types=1);

namespace StarDict;

use DateTime;

class Dict
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getVersion(): string
    {
        return (string) $this->data['version'] ?? '';        
    }

    public function getBookname(): string
    {
        return (string) $this->data['bookname'] ?? '';
    }

    public function getWordCount(): int
    {
        return (int) $this->data['wordcount'] ?? 0;
    }

    public function getIndexFilesize(): int
    {
        return (int) $this->data['idxfilesize'] ?? 0;
    }

    public function getAuthor(): string
    {
        return (string) $this->data['author'] ?? '';
    }

    public function getEmail(): string
    {
        return (string) $this->data['email'] ?? '';
    }

    public function getWebsite(): string
    {
        return (string) $this->data['website'] ?? '';
    }

    public function getDescription(): string
    {
        return (string) $this->data['description'] ?? '';
    }

    public function getDate(): DateTime
    {
        return $this->data['date'];
    }

    public function getSameTypeSequence(): string
    {
        return (string) $this->data['sametypesequence'] ?? '';
    }
}