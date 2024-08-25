fetch('questions.json')
    .then(response => response.json())
    .then(data => {
        questions = data.questions.map(q => ({
            question: q.Question,
            difficulty: q.Difficulty,
            answers: [
                { text: q.Answer1, index: 1 },
                { text: q.Answer2, index: 2 },
                { text: q.Answer3, index: 3 },
                { text: q.Answer4, index: 4 }
            ],
            rightAnswer: q.rightAnswer
        }));
        startQuiz();
    });

const questionElement = document.getElementById("question");
const difficultyElement = document.getElementById("difficulty");
const answerButtons = document.getElementById("answer-buttons");
const nextButton = document.getElementById("next-question-button");
const skipButton = document.getElementById("skip-question-button");
const homeButton = document.getElementById("home-button");


let currentIndex = 0;
let score = 0;

function startQuiz() {
    currentIndex = 0;
    score = 0;
    nextButton.innerHTML = "Sonraki Soru";
    progressBarPercentage = 0;
    showQuestion();
    move();
}

function showQuestion() {
    resetState();
    move();
    let currentQuestion = questions[currentIndex];
    let questionNumber = currentIndex + 1;
    questionElement.innerHTML = questionNumber + ". " + currentQuestion.question;

    difficultyElement.innerHTML = "Zorluk: " +  getScoreByDifficultyStr(currentQuestion.difficulty);

    currentQuestion.answers.forEach(answer => {
        const button = document.createElement("button");
        button.innerHTML = answer.text;
        button.classList.add("button1");
        answerButtons.appendChild(button);
        button.dataset.index = answer.index;
        button.addEventListener("click", selectAnswer);
    });

    skipButton.style.display = "block";
    skipButton.disabled = false;
}

function resetState() {
    nextButton.style.display = "none";
    skipButton.style.display = "none";
    while (answerButtons.firstChild) {
        answerButtons.removeChild(answerButtons.firstChild);
    }
}

function selectAnswer(e) {
    const selectedButton = e.target;
    const selectedIndex = selectedButton.dataset.index; 
    const correctIndex = questions[currentIndex].rightAnswer; 

    if (selectedIndex == correctIndex) {
        selectedButton.classList.add("correct");
        score += getScoreByDifficulty(questions[currentIndex].difficulty);
    } else {
        selectedButton.classList.add("incorrect");
        Array.from(answerButtons.children).forEach(button => {
            if (button.dataset.index == correctIndex) {
                button.classList.add("correct");
            }
        });
    }

    Array.from(answerButtons.children).forEach(button => {
        button.disabled = true;
    });

    if (currentIndex < questions.length - 1) {
        nextButton.innerHTML = "Sonraki Soru";
        nextButton.style.display = "block";
    } else {
        nextButton.innerHTML = "Sınavı Bitir"; 
        nextButton.style.display = "block";
    }

    skipButton.disabled=true;
}

function getScoreByDifficulty(difficulty) {
    switch (difficulty) {
        case "easy":
            return 1;
        case "medium":
            return 2;
        case "hard":
            return 3;
        default:
            return 0;
    }
}

function getScoreByDifficultyStr(difficulty) {
    switch (difficulty) {
        case "easy":
            return "Kolay";
        case "medium":
            return "Orta";
        case "hard":
            return "Zor";
        default:
            return "";
    }
    }

function showScore() {
    resetState();
    questionElement.innerHTML = `Quiz bitti. ${questions.length} sorudan puanınız: ${score}`;
    difficultyElement.innerHTML = "";
    nextButton.innerHTML = "Tekrar Dene";
    nextButton.style.display = "block";
    homeButton.style.display = "block";
}

function handleNextButton() {
    currentIndex++;
    if (currentIndex < questions.length) {
        showQuestion();
    } else {
        showScore();
    }
    move();
}

function skipQuestion() {
    handleNextButton();
}

nextButton.addEventListener("click", () => {
    if (currentIndex < questions.length) {
        handleNextButton();
    } else {
        startQuiz();
        homeButton.style.display = "none";
    }
});

skipButton.addEventListener("click", skipQuestion);

homeButton.addEventListener("click", () => {
    window.location.href = 'index.html';  
});

function move() {
    var elem = document.getElementById("myBar");
    var width = (currentIndex / questions.length) * 100; //mybar w3 schoolsdan bakarak yaptım
    elem.style.width = width + "%";
    elem.innerHTML = Math.round(width) + "%";
}