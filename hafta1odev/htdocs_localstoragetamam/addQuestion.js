let questionsData = JSON.parse(localStorage.getItem('questions'));
let questions = questionsData.questions;

lastPlusOneQuestionIndex=questions.length;
console.log(lastPlusOneQuestionIndex);
console.log(questions)
function saveQuestion () {
    const index= parseInt(lastPlusOneQuestionIndex);
    const Question = document.getElementById("question").value;
    const Answer1 = document.getElementById("answer1").value;
    const Answer2 = document.getElementById("answer2").value;
    const Answer3 = document.getElementById("answer3").value;
    const Answer4= document. getElementById("answer4").value;
    const rightAnswer = document.getElementById("rightAnswer").value;
    const Difficulty = document.getElementById("difficultya").value;
    questions[lastPlusOneQuestionIndex] = {
      index,
      Question,
      Difficulty,
      Answer1,
      Answer2,
      Answer3,
      Answer4,
      rightAnswer
    };
    questionsData.questions = questions;
    localStorage.setItem('questions', JSON.stringify(questionsData));
  
  }