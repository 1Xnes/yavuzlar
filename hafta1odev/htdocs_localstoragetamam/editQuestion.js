const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);  //kaynağım https://www.sitepoint.com/get-url-parameters-with-javascript/
const questId = urlParams.get('questionId')
console.log(questId); // sorunun kaçıncı soru olduğunu aldık


        
let questionsData = JSON.parse(localStorage.getItem('questions'));
let questions = questionsData.questions;
        



const question = questions[parseInt(questId)];
document.getElementById("question").value = question.Question;
document.getElementById("answer1").value = question.Answer1;
document.getElementById("answer2").value = question.Answer2;
document.getElementById("answer3").value = question.Answer3;
document.getElementById("answer4").value = question.Answer4;
document.getElementById("rightAnswer").value = question.rightAnswer;
document.getElementById("difficultya").value = question.Difficulty



function saveQuestion () {
  const index= parseInt(questId+1);
  const Question = document.getElementById("question").value;
  const Answer1 = document.getElementById("answer1").value;
  const Answer2 = document.getElementById("answer2").value;
  const Answer3 = document.getElementById("answer3").value;
  const Answer4= document. getElementById("answer4").value;
  const rightAnswer = document.getElementById("rightAnswer").value;
  const Difficulty = document.getElementById("difficultya").value;
  questions[questId] = {
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