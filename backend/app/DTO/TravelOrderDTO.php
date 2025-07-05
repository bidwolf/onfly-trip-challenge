<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

readonly class TravelOrderDTO
{
  public function __construct(
    public string $requester_name,
    public string $destination,
    public Carbon $departure_date,
    public Carbon $return_date,
    public float $price,
    public ?string $hosting,
    public ?string $description,
    public ?string $transportation
  ) {}
  /**
   * Creates the TravelOrderDTO from an array.
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self
  {
    return new self(
      requester_name: $data['requester_name'],
      destination: $data['destination'],
      departure_date: Carbon::parse($data['departure_date']),
      return_date: Carbon::parse($data['return_date']),
      price: $data['price'] ?? "0.0",
      hosting: array_key_exists('hosting', $data) ? $data['hosting'] : "",
      transportation: array_key_exists('transportation', $data) ? $data['transportation'] : "",
      description: array_key_exists('description', $data) ? $data['description'] : "",
    );
  }
}
