import React from 'react';

function GlobalComment() {
    const comments = ['Comment 1', 'Comment 2'];
    return (
        <div className="global-comment-container">
            <ul className="comments">
                {comments.map((comment) => (
                    <li>{comment}</li>
                ))}
            </ul>
            <input type="text" placeholder="Enter Comment" />
        </div>
    );
}

export default GlobalComment;
