import { Dropdown } from 'antd';
import React from 'react';
import commentIcon from '../../images/comment-blue.svg';
import DownloadIcon from '../../images/download.svg';
import ModalButton from '../ModalButton/ModalButton';
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

function DownloadButton({ identifier, location, graphID }) {
    const downloadPNG = id => {
        const chartData = document.getElementById(id).toDataURL('image/png');

        const link = document.createElement('a');
        link.download = 'evnet-by-year.png';
        link.href = chartData;
        link.click();
        link.remove();
        // document.body.removeChild(link);
    };
    const assetsPath = window.eluxDashboard.assetsUrl;
    const onClick = ({ key }) => {
        switch (identifier) {
            case 1: {
                if (key === 'download_as_xls') {
                    console.log('Downloading csv');
                } else if (key === 'download_as_svg') {
                    console.log('Downloading PNG');
                    downloadPNG('event-by-year-graph');
                }
                break;
            }
            case 4: {
                if (key === 'download_as_xls') {
                    console.log('Downloading csv');
                } else if (key === 'download_as_svg') {
                    console.log('Downloading PNG');
                    downloadPNG('eventCategoryChartRef');
                }
                break;
            }
            default:
                console.log('Nothing to download');
        }
    };
    return (
        <div className="download-button-wrapper">
            <ModalButton location={location} graphID={graphID}>
                <img src={assetsPath + commentIcon} alt="comment icon" />
            </ModalButton>

            <Dropdown
                menu={{
                    items,
                    onClick,
                }}
                placement="bottomRight"
                arrow
            >
                <button type="button" onClick={e => e.preventDefault()}>
                    <img src={assetsPath + DownloadIcon} alt="comment icon" />
                </button>
            </Dropdown>
        </div>
    );
}

export default DownloadButton;
