<?php

use PHPUnit\Framework\TestCase;
use App\Services\Rating\RatingService;
use App\Models\Interfaces\IRating;
use App\Repository\IQuestRepository;
use App\Repository\IUserRepository;
use App\Repository\Rating\IRatingRepository;

class RatingServiceTest extends TestCase
{
  private $ratingRepository;
  private $questRepository;
  private $userRepository;
  private $ratingService;

  protected function setUp(): void
  {
    $this->ratingRepository = $this->createMock(IRatingRepository::class);
    $this->questRepository = $this->createMock(IQuestRepository::class);
    $this->userRepository = $this->createMock(IUserRepository::class);

    $this->ratingService = new RatingService($this->ratingRepository, $this->questRepository, $this->userRepository);
  }

  public function testAddRating()
  {
    $rating = $this->createMock(IRating::class);

    $this->ratingRepository->expects($this->once())
      ->method('addRating')
      ->with($rating);

    $this->ratingService->addRating($rating);
  }

  public function testGetUserItemMatrix()
  {
    $ratings = [
      $this->createMock(IRating::class),
      $this->createMock(IRating::class),
    ];

    $maxUserId = 10;
    $maxQuestId = 5;

    $this->ratingRepository->expects($this->once())
      ->method('getRatings')
      ->willReturn($ratings);

    $this->userRepository->expects($this->once())
      ->method('getMaxUserId')
      ->willReturn($maxUserId);

    $this->questRepository->expects($this->once())
      ->method('getMaxQUestId')
      ->willReturn($maxQuestId);

    $userItemMatrix = $this->ratingService->getUserItemMatrix();

    $this->assertCount($maxUserId + 1, $userItemMatrix);
    $this->assertCount($maxQuestId + 1, $userItemMatrix[0]);
    $this->assertEquals(0, $userItemMatrix[0][0]);
  }
}