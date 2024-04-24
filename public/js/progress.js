function updateProgressBar(overallScore, overallMaxScore, maxScoreUntilNow) {
  const maxProgress = (maxScoreUntilNow / overallMaxScore) * 100;
  const currentProgress = (overallScore / overallMaxScore) * 100;

  document.querySelector(".progress").style.width = currentProgress + "%";
  document.querySelector(".max-progress").style.width = maxProgress - currentProgress + "%";
}
