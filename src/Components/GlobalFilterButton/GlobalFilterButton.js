import React from 'react';
import commentIcon from '../../images/comment.svg';
import FilterIcon from '../../images/filter.svg';
import { eluxTranslation } from '../../Translation/Translation';
import ModalButton from '../ModalButton/ModalButton';
import './GlobalFilterButton.scss';

function GlobalFilterButton() {
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { filters } = eluxTranslation;
    return (
        <div className="global-filter-button">
            <ModalButton location="global-comment">
                <img src={assetsPath + commentIcon} alt="comment icon" />
            </ModalButton>
            <button type="button">
                <img src={assetsPath + FilterIcon} alt="filter icon" />
                {filters}
            </button>
        </div>
    );
}

export default GlobalFilterButton;
