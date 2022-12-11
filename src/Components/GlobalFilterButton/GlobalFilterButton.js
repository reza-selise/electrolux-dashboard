import React from 'react';
import commentIcon from '../../images/comment.svg';
import FilterIcon from '../../images/filter.svg';
import ModalButton from '../ModalButton/ModalButton';
import './GlobalFilterButton.scss';

function GlobalFilterButton() {
    const assetsPath = window.eluxDashboard.assetsUrl;
    return (
        <div className="global-filter-button">
            <ModalButton location="global-comment">
                <img src={assetsPath + commentIcon} alt="comment icon" />
            </ModalButton>
            <button type="button">
                <img src={assetsPath + FilterIcon} alt="filter icon" />
                Filters
            </button>
        </div>
    );
}

export default GlobalFilterButton;
