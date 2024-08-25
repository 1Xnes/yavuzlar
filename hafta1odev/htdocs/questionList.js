fetch('questions.json')
    .then(response => response.json())
    .then(data => {
        const tbody = document.querySelector('tbody');
        
        data.questions.forEach((q, index) => {
            const tr = document.createElement('tr');
            const tdIndex = document.createElement('td');
            tdIndex.textContent = `${index + 1}. soru`;
            
            
            
            
            
            
            tr.appendChild(tdIndex);
            const tdQuestion = document.createElement('td');
            tdQuestion.textContent = q.Question;
            tr.appendChild(tdQuestion);
            for (let i = 1; i <= 4; i++) {
                const tdAnswer = document.createElement('td');
                tdAnswer.textContent = q[`Answer${i}`];
                tr.appendChild(tdAnswer);
            }




            const tdRightAnswer = document.createElement('td');
            tdRightAnswer.textContent = q.rightAnswer;
            tr.appendChild(tdRightAnswer);
            const tdDifficulty = document.createElement('td');
            tdDifficulty.textContent = q.Difficulty;
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
                tr.remove();  // jsondan silmeyi beceremedim sadece anlık sitede silinmiş şeklini yapmayı seçtim ben de
            });
            tdDelete.appendChild(deleteLink);
            tr.appendChild(tdDelete);
            tbody.appendChild(tr);
        });
    })