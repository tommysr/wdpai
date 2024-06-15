<?php
use PHPUnit\Framework\TestCase;
use App\Services\Authorize\Quest\QuestRequest;

class QuestRequestTest extends TestCase
{
  public function testFromActionReturnsAccessForShowQuests()
  {
    $action = 'showQuests';
    $expected = QuestRequest::ACCESS;

    $result = QuestRequest::fromAction($action);

    $this->assertEquals($expected, $result);
  }

  public function testFromActionReturnsAccessForEnterQuest()
  {
    $action = 'enterQuest';
    $expected = QuestRequest::ACCESS;

    $result = QuestRequest::fromAction($action);

    $this->assertEquals($expected, $result);
  }

  public function testFromActionReturnsAccessForShowQuestWallets()
  {
    $action = 'showQuestWallets';
    $expected = QuestRequest::ACCESS;

    $result = QuestRequest::fromAction($action);

    $this->assertEquals($expected, $result);
  }

  public function testFromActionReturnsEditForEditQuest()
  {
    $action = 'editQuest';
    $expected = QuestRequest::EDIT;

    $result = QuestRequest::fromAction($action);

    $this->assertEquals($expected, $result);
  }

  public function testFromActionReturnsEditForShowEditQuest()
  {
    $action = 'showEditQuest';
    $expected = QuestRequest::EDIT;

    $result = QuestRequest::fromAction($action);

    $this->assertEquals($expected, $result);
  }

  public function testFromActionReturnsNullForUnknownAction()
  {
    $action = 'unknownAction';

    $result = QuestRequest::fromAction($action);

    $this->assertNull($result);
  }
}