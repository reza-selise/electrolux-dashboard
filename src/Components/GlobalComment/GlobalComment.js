import React from 'react';
import rocketIcon from '../../images/rocket.svg';
import { eluxTranslation } from '../../Translation/Translation';
import './GlobalComment.scss';

function GlobalComment() {
    const comments = [
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. ',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. ',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
        't is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
    ];
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { startTyping } = eluxTranslation;
    return (
        <div className="global-comment-container">
            <ul className="comments">
                {comments.map((comment) => (
                    <li>{comment}</li>
                ))}
            </ul>
            <form className="comment-submit-wrapper">
                <input type="text" placeholder={startTyping} />
                <button type="submit">
                    <img src={assetsPath + rocketIcon} alt="rocket icon" />
                </button>
            </form>
        </div>
    );
}

export default GlobalComment;
