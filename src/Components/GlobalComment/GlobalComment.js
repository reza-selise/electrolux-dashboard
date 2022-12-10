import React from 'react';
import rocketIcon from '../../images/rocket.svg';
import './GlobalComment.scss';

function GlobalComment() {
    const comments = [
        'Comment 1',
        'Comment 2',
        'Comment 3',
        'Comment 4',
        'Comment 5',
        'Comment 6',
        'Comment 7',
        'Comment 8',
        'Comment 9',
        'Comment 10',
        'Comment 11',
        'Comment 12',
        'Comment 13',
    ];
    const assetsPath = window.eluxDashboard.assetsUrl;
    return (
        <div className="global-comment-container">
            <ul className="comments">
                {comments.map((comment) => (
                    <li>{comment}</li>
                ))}
            </ul>
            <form className="comment-submit-wrapper">
                <input type="text" placeholder="Start typing..." />
                <button type="submit">
                    <img src={assetsPath + rocketIcon} alt="rocket icon" />
                </button>
            </form>
        </div>
    );
}

export default GlobalComment;
