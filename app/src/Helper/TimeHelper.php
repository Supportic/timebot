<?php

declare(strict_types=1);

namespace App\Helper;

use DateInterval;
use DateTime;

class TimeHelper
{
  public function calculateRemainingTime(int $timestamp): DateInterval
  {
    $now = new DateTime();
    $expirationDate = (new DateTime())
      ->setTimestamp($timestamp);
    return $expirationDate->diff($now);
  }

  public function formatDateInterval(DateInterval $dateInterval): string
  {

    $segments = [];

    if ($dateInterval->h) {
      $segments[] = $dateInterval->format(
        "%h hour" . ($dateInterval->h > 1 ? 's' : '')
      );
    }
    if ($dateInterval->i) {
      $segments[] = $dateInterval->format(
        "%i minute" . ($dateInterval->i > 1 ? 's' : '')
      );
    }
    if ($dateInterval->s) {
      $segments[] = $dateInterval->format(
        "%s second" . ($dateInterval->s > 1 ? 's' : '')
      );
    }

    return implode(', ', $segments);
  }
}
