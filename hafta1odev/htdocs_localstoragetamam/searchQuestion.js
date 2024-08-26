
 // https://stackoverflow.com/questions/8517089/js-search-in-object-values 'den çalıntı bi çözüm
let questionsData = JSON.parse(localStorage.getItem('questions'));
let questions = questionsData.questions;
let object= questions;
function search() {
    let searchText = document.getElementById('searchInput').value.toLowerCase();
    let arrayOfMatchedObjects = questions.filter(object => {
      return JSON.stringify(object)
        .toLowerCase()
        .includes(searchText);
    });
 console.log(arrayOfMatchedObjects);
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        arrayOfMatchedObjects.forEach((q, index) => {

        

        const tr = document.createElement('tr');
        const tdIndex = document.createElement('td');
        tdIndex.textContent = `${q.index + 1}. soru`;
        currentQuestion = arrayOfMatchedObjects[index];
        
        
        tr.appendChild(tdIndex);
        const tdQuestion = document.createElement('td');
        tdQuestion.textContent = q.Question;
        tr.appendChild(tdQuestion);
        


        const tdAnswer1 = document.createElement('td');
            tdAnswer1.textContent = q.Answer1;
            tr.appendChild(tdAnswer1);
        const tdAnswer2 = document.createElement('td');
            tdAnswer2.textContent = q.Answer2;
            tr.appendChild(tdAnswer2);
            const tdAnswer3 = document.createElement('td');
            tdAnswer3.textContent = q.Answer3;
            tr.appendChild(tdAnswer3);
        const tdAnswer4 = document.createElement('td');
            tdAnswer4.textContent = q.Answer4;
            tr.appendChild(tdAnswer4);
       
        


        

        const tdRightAnswer = document.createElement('td');
        tdRightAnswer.textContent = q.rightAnswer;
        tr.appendChild(tdRightAnswer);


        const tdDifficulty = document.createElement('td');
        tdDifficulty.textContent = q.Difficulty;
        tr.appendChild(tdDifficulty);


        tbody.appendChild(tr);
    });
}