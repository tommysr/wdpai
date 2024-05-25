<!-- <?php

namespace App\Services\Register;

use App\Repository\IUserRepository;
use App\Services\Register\IRegisterStrategyFactory;
use App\Request\IFullRequest;

class StrategyFactory implements IRegisterStrategyFactory
{
  private IUserRepository $userRepository;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository)
  {
    $this->request = $request;
    $this->userRepository = $userRepository;
  }

  public function create(string $method): IRegisterStrategy
  {
    if ($method === 'db') {
      return new DbRegisterStrategy($this->request, $this->userRepository);
    } else {
      throw new \Exception('Invalid method');
    }
  }
}