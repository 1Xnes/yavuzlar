if (typeof questionsData === "undefined") {
  

const questionsData = {
    
    "questions": [
      {"index": 1, "Difficulty": "hard", "Question": "Soru1", "Answer1": "yanliscevap3", "Answer2": "dogrucevap", "Answer3": "yanliscevap2", "Answer4": "yanliscevap1", "rightAnswer": 2},
      {"index": 2, "Difficulty": "easy", "Question": "Soru2", "Answer1": "yanliscevap1", "Answer2": "yanliscevap2", "Answer3": "dogrucevap", "Answer4": "yanliscevap3", "rightAnswer": 3},
      {"index": 3, "Difficulty": "medium", "Question": "Soru3", "Answer1": "dogrucevap", "Answer2": "yanliscevap1", "Answer3": "yanliscevap3", "Answer4": "yanliscevap2", "rightAnswer": 1},
      {"index": 4, "Difficulty": "hard", "Question": "Soru4", "Answer1": "yanliscevap2", "Answer2": "yanliscevap1", "Answer3": "yanliscevap3", "Answer4": "dogrucevap", "rightAnswer": 4},
      {"index": 5, "Difficulty": "medium", "Question": "Soru5", "Answer1": "yanliscevap3", "Answer2": "dogrucevap", "Answer3": "yanliscevap1", "Answer4": "yanliscevap2", "rightAnswer": 2},
      {"index": 6, "Difficulty": "easy", "Question": "Soru6", "Answer1": "dogrucevap", "Answer2": "yanliscevap3", "Answer3": "yanliscevap1", "Answer4": "yanliscevap2", "rightAnswer": 1},
      {"index": 7, "Difficulty": "hard", "Question": "Soru7", "Answer1": "yanliscevap1", "Answer2": "yanliscevap3", "Answer3": "dogrucevap", "Answer4": "yanliscevap2", "rightAnswer": 3},
      {"index": 8, "Difficulty": "medium", "Question": "Soru8", "Answer1": "yanliscevap2", "Answer2": "yanliscevap1", "Answer3": "yanliscevap3", "Answer4": "dogrucevap", "rightAnswer": 4},
      {"index": 9, "Difficulty": "easy", "Question": "Soru9", "Answer1": "yanliscevap1", "Answer2": "dogrucevap", "Answer3": "yanliscevap2", "Answer4": "yanliscevap3", "rightAnswer": 2},
      {"index": 10, "Difficulty": "hard", "Question": "Soru10", "Answer1": "yanliscevap3", "Answer2": "yanliscevap2", "Answer3": "yanliscevap1", "Answer4": "dogrucevap", "rightAnswer": 4}
    ]
  }
localStorage.setItem('questions', JSON.stringify(questionsData));
const storedQuestions = JSON.parse(localStorage.getItem('questions'));
console.log(storedQuestions);
}

