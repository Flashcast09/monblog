

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// POUR ENABLE LES DROPDOWN VIA BOOTSTRAP
import {Dropdown} from "bootstrap";


/* Listening to the DOMContentLoaded event. */
document.addEventListener('DOMContentLoaded', () => {
    new App();
});

class App {
    constructor() {
        this.enableDropdowns();
        this.handleCommentForm();
    }

    enableDropdowns() {
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    dropdownElementList.map(function(dropdownToggleEl){
        return new Dropdown(dropdownToggleEl)});
    }

    handleCommentForm() {
        
        const commentForm = document.querySelector('form.comment-form');
        
        if (null === commentForm) {
            return;
        }
            
        /* Listening to the submit event of the comment form. */
        commentForm.addEventListener('submit', async (e) => {
            /* It prevents the default behavior of the form. */
            e.preventDefault();

            const response = await fetch('/ajax/comments', {
                method: 'POST',
                body: new FormData(e.target)
            });

            /* Checking if the response is ok. */
            if (!response.ok){
                return;
            }

            const json = await response.json();
            
            if(json.code === 'COMMENT_ADDED_SUCCESSFULLY'){
                /* Selecting the element with the class comment-list. */
                const commentList = document.querySelector('.comment-list');
                /* Selecting the element with the class comment-count. */
                const commentCount = document.querySelector('.comment-count');
                /* Selecting the element with the id comment_content. */
                const commentContent = document.getElementById('comment_content');
                commentList.insertAdjacentHTML('afterbegin', json.message);
                commentCount.innerText = json.numberOfComments;
                /* Emptying the comment form after the comment is submitted. */
                commentContent.value = '';
                
                
                
            }
        });
    }
}



