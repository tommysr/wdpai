
function updateProgressBar(overallScore, overallMaxScore) {
  const progress = document.querySelector(".progress");
  const currentProgress = (overallScore / overallMaxScore) * 100;
  progress.style.width = currentProgress + "%";
  // document.querySelector(".max-progress").style.width = maxProgress - currentProgress + "%";
}
