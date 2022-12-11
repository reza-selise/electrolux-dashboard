import { Select } from 'antd';
import React from 'react';
import { useGetGenericCommentQuery } from '../../API/apiSlice';
import rocketIcon from '../../images/rocket.svg';
import { eluxTranslation } from '../../Translation/Translation';
import './GlobalComment.scss';

function GlobalComment() {
    const { data, error, isLoading } = useGetGenericCommentQuery();
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { startTyping, errorOccured, pleaseWait } = eluxTranslation;
    const handleYearChange = (value) => {
        console.log(value);
    };
    return (
        <div className="global-comment-container">
            <div className="comment-filters">
                <Select
                    defaultValue="2022"
                    style={{ width: 200, height: 54 }}
                    onChange={handleYearChange}
                    options={[
                        {
                            value: '2022',
                            label: '2022',
                        },
                        {
                            value: '2021',
                            label: '2021',
                        },
                        {
                            value: '2020',
                            label: '2020',
                        },
                    ]}
                />
            </div>
            <ul className="comments">
                {error
                    ? errorOccured
                    : isLoading
                    ? pleaseWait
                    : data.data.map((comment) => (
                          <li key={comment.comment_ID}>{comment.comment_content}</li>
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
