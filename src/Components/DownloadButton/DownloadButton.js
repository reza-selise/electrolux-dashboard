import { Dropdown } from 'antd';
import React from 'react';
import commentIcon from '../../images/comment-blue.svg';
import DownloadIcon from '../../images/download.svg';
import './DownloadButton.scss';

const items = [
    {
        label: 'Download as SVG',
        key: 'download_as_svg',
    },
    {
        label: 'Download as XLS',
        key: 'download_as_xls',
    },
];

function DownloadButton() {
    const assetsPath = window.eluxDashboard.assetsUrl;
    const onClick = ({ key }) => {
        console.log(key);
    };
    return (
        <div className="download-button-wrapper">
            <button type="button">
                <img src={assetsPath + commentIcon} alt="comment icon" />
            </button>
            <Dropdown
                menu={{
                    items,
                    onClick,
                }}
                placement="bottomRight"
                arrow
            >
                <button type="button" onClick={(e) => e.preventDefault()}>
                    <img src={assetsPath + DownloadIcon} alt="comment icon" />
                </button>
            </Dropdown>
        </div>
    );
}

export default DownloadButton;
