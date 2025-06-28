<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

readonly class TravelOrderDTO
{
  public function __construct(
    public string $requester_name,
    public string $destination,
    public Carbon $departure_date,
    public Carbon $return_date
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
      return_date: Carbon::parse($data['return_date'])
    );
  }
}
