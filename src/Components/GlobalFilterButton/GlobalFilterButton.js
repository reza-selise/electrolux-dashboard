import React, { useState } from 'react';
import '../../../inc/assets/css/elux-font.css';
import commentIcon from '../../images/comment.svg';
import FilterIcon from '../../images/filter.svg';
import { eluxTranslation } from '../../Translation/Translation';
import CustomDrawer from '../CustomDrawer/CustomDrawer';
import ModalButton from '../ModalButton/ModalButton';
import './GlobalFilterButton.scss';

function GlobalFilterButton() {
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { filters } = eluxTranslation;
    const [open, setOpen] = useState(false);

    const showDrawer = () => {
        setOpen(true);
    };

    const onClose = () => {
        setOpen(false);
    };
    return (
        <>
            <div className="global-filter-button">
                <ModalButton location="global-comment">
                    <img src={assetsPath + commentIcon} alt="comment icon" />
                </ModalButton>
                <button type="button" onClick={showDrawer}>
                    <img src={assetsPath + FilterIcon} alt="filter icon" />
                    {filters}
                </button>
            </div>
            <CustomDrawer open={open} onClose={onClose} />
        </>
    );
}

export default GlobalFilterButton;
