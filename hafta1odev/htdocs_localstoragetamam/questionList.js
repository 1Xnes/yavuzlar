
        let questions = [];
        const storedQuestions = JSON.parse(localStorage.getItem('questions'));
        console.log(storedQuestions);
        questions = storedQuestions.questions.map(q => ({
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
        let questionCounter = 0;
        const tbody = document.querySelector('tbody');
        questions.forEach((q, index) => {
            const tr = document.createElement('tr');
            const tdIndex = document.createElement('td');
            tdIndex.textContent = `${index + 1}. soru`;
            currentQuestion = questions[index];
            
            
            
            
            
            tr.appendChild(tdIndex);
            const tdQuestion = document.createElement('td');
            tdQuestion.textContent = q.question;
            tr.appendChild(tdQuestion);

            currentQuestion.answers.forEach(answer => {
                const tdAnswer = document.createElement('td');
                tdAnswer.textContent = answer.text;
                tr.appendChild(tdAnswer);
            });            
            


            

            const tdRightAnswer = document.createElement('td');
            tdRightAnswer.textContent = q.rightAnswer;
            tr.appendChild(tdRightAnswer);


            const tdDifficulty = document.createElement('td');
            tdDifficulty.textContent = q.difficulty;
            tr.appendChild(tdDifficulty);


            const tdEdit = document.createElement('td');
            const editLink = document.createElement('a');
            editLink.href = `editQuestion.html?questionId=${index}`;
            editLink.textContent = 'Düzenle';
            tdEdit.appendChild(editLink);
            tr.appendChild(tdEdit);
            const tdDelete = document.createElement('td');
            const deleteLink = document.createElement('a');
            deleteLink.href = '#';
            deleteLink.textContent = 'Sil';
            deleteLink.addEventListener('click', () => {
                tr.remove();
                deleteQuestion(index);    });
            tdDelete.appendChild(deleteLink);
            tr.appendChild(tdDelete);
            tbody.appendChild(tr);
        });

        function deleteQuestion(index) {
            let questionsData = JSON.parse(localStorage.getItem('questions'));
            let questions = questionsData.questions;
            questions.splice(index, 1);
            for (let i = 0; i < questions.length; i++) {
                questions[i].index = i;
            }
            localStorage.setItem('questions', JSON.stringify(questionsData));
            location.reload()
        }
