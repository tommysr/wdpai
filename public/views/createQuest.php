<script type="text/javascript" src="/public/js/questCreate.js" defer></script>
<main class="flex-column-center-center">
  <form id="questForm" class="container" onsubmit="submitForm(event)">
    <div class="cards-create">

      <div class="card flex-column-center">
        <?php if (isset($userRole) && $userRole == 'creator'): ?>
          <h1 class="main-text">Create Quiz</h1>
          <h2 class="input-description main-text">Thumbnail</h2>
          <div class="file-input-container">
          <input type="file" id="fileInput" class="file-input" name="file" accept="image/*">
            <label for="fileInput" class="file-label">Choose File</label>
          </div>
        <?php endif; ?>
        <img id="preview" src="<?= $quest ? "/public/uploads/" . $quest->getPictureUrl() : '#' ?>"
          alt="Image preview" />

        <?php if (isset($userRole) && $userRole == 'creator'): ?>
          <button type="button" class="upload-button" onclick="uploadFile()">Upload</button>
        <?php endif; ?>
      </div>


      <div class="flex-column-center-center gap-1 card">
        <input type="hidden" id="questThumbnail" name="questThumbnail"
          value="<?= $quest ? $quest->getPictureUrl() : '' ?>" />

        <label for="quizTitle" class="input-description main-text">Quiz Title:</label>
        <input class="login-input" type="text" id="quizTitle" name="title" placeholder="title" minlength="5"
          maxlength="90" value="<?= $quest ? $quest->getTitle() : '' ?>" required>


        <label for="quizDescription" class="input-description main-text">Quiz
          Description:</label>
        <textarea name="description" id="description" cols="50" rows="10" placeholder="description" class="main-text-light"
          minlength="20" maxlength="300" required><?= $quest ? $quest->getDescription() : '' ?></textarea>

        <label for="requiredWallet" class="input-description main-text">Required wallet:</label>
        <input type="text" class="login-input" name="blockchain" placeholder="required wallet" minlength="3"
          maxlength="50" value="<?= $quest ? $quest->getBlockchain() : '' ?>" required>


        <table>
          <tr>
            <td>
              <div class="flex-column-center"> <label for="expiryDate" class="input-description main-text">Expiry
                  date:</label>
                <input type="date" class="login-input" name="expiryDate"
                  value="<?= $quest ? $quest->getExpiryDateString() : '' ?>" required>
              </div>
            </td>
            <td>
              <div class="flex-column-center"> <label for="payoutDate" class="input-description main-text">Payout
                  date:</label>
                <input type="date" class="login-input" name="payoutDate"
                  value="<?= $quest ? $quest->getPayoutDate() : '' ?>" required>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="flex-column-center"> <label for="timeRequired"
                  class="input-description main-text">Minutes</label>
                <input type="number" class="login-input" name="minutesRequired" min="1" max="120" placeholder="minutes"
                  value="<?= $quest ? $quest->getRequiredMinutes() : '' ?>" required>

              </div>
            </td>
            <td>
              <div class="flex-column-center"> <label for="participantsLimit"
                  class="input-description main-text">Participants:</label>
                <input type="number" class="login-input" name="participantsLimit" min="20" max="1000"
                  placeholder="participants" value="<?= $quest ? $quest->getParticipantsLimit() : '' ?>" required>

              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="flex-column-center"> <label for="poolAmount" class="input-description main-text">Pool
                  amount:</label>
                <input type="text" class="login-input" name="poolAmount"
                  value="<?= $quest ? $quest->getPoolAmount() : '' ?>" placeholder="pool amount" required>
              </div>
            </td>
            <td>
              <div class="flex-column-center">
                <label for="poolAmount" class="input-description main-text">Payout token:</label>
                <input type="text" class="login-input" name="token" value="<?= $quest ? $quest->getToken() : '' ?>"
                  required placeholder="token" minlength="3" maxlength="20">
              </div>
            </td>
          </tr>
        </table>

      </div>
      <?php
      $counter = 0;
      ?>

      <div class="flex-column-center-center questions">
        <h2 class="main-text">Questions:</h2>
        <div class="cards">
          <?php
          if ($quest):
            $questions = $quest->getQuestions();
            foreach ($questions as $question):
              $questionId = $question->getQuestionId();
              $optionCounter = 0;
              ?>
              <div class="card">
                <div class="container-card bg-green-box question flex-column-center-center gap-1">
                  <input type="hidden" name="questions[<?= $counter; ?>][id]" value="<?= $questionId; ?>">
                  <textarea name="questions[<?= $counter; ?>][text]" class="questionText main-text-light" cols="30" rows="10"
                    minlength="3" maxlength="500" placeholder="question text"
                    required><?= $question->getText(); ?></textarea>

                  <div class="grid-2">
                    <label for="questionPoints" class="input-description main-text center">Points:</label>
                    <input class="points questionPoints" type="number" name="questions[<?= $counter; ?>][score]" min="1"
                      max="100" value="<?= $question->getPoints() ?>" required>
                  </div>

                  <div class="options flex-column-center-center gap-1">
                    <?php
                    foreach ($question->getOptions() as $option):
                      $optionId = $option->getOptionId();

                      ?>

                      <div class="grid-3 w-100">
                        <div>
                          <input type="hidden" name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][id]"
                            value="<?= $optionId; ?>">
                          <input class="points optionText" type="text"
                            name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][text]" minlength="1"
                            maxlength="50" value="<?= $option->getText() ?>" required>
                        </div>

                        <div class="option-container">
                          <input type="checkbox"
                            name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][isCorrect]"
                            <?= $option->getIsCorrect() ? 'checked' : '' ?> value=true />
                          <span class="checkmark"></span>
                        </div>

                        <?php if (isset($userRole) && $userRole == 'creator'): ?>
                          <div class="flex-row-center-center">
                            <button type="button" class="removeOption show-more-btn bg-green-box"
                              onclick="removeOption(this,<?= $counter; ?>,<?= $optionCounter; ?>)">remove</button>
                          </div>
                        <?php endif; ?>

                      </div>
                      <?php
                      $optionCounter = $optionCounter + 1;
                    endforeach;
                    ?>
                  </div>

                  <?php if (isset($userRole) && $userRole == 'creator'): ?>
                    <button type="button" class="addOption main-button w-50"
                      onclick="addOptionRaw(this, <?= $counter; ?>,<?= $optionCounter; ?> )">Add
                      Option</button>
                    <button type="button" class="removeQuestion secondary-button w-50"
                      onclick="removeQuestion(this,<?= $counter; ?>)">Remove Question</button>
                  <?php endif; ?>
                </div>
              </div>
              <?php
              $counter = $counter + 1;
            endforeach;
          endif; ?>

        </div>
        <?php if (isset($userRole) && $userRole == 'creator'): ?>
          <div class="flex-column-center-center gap-1 m-20">
            <span id="error" class="error-message"></span>
            <button type="button" id="addQuestion" class="main-button" onclick="addQuestionRaw(<?= $counter; ?>)">Add
              Question</button>
            <button type="submit" class="secondary-button">Create Quiz</button>
          </div>
        <?php endif; ?>

      </div>
  </form>
  </div>

</main>