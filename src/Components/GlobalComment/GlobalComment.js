import { Select } from 'antd';
import React, { useRef, useState } from 'react';
import { useGetGenericCommentQuery, useInsetGenericCommentMutation } from '../../API/apiSlice';
import rocketIcon from '../../images/rocket.svg';
import { eluxTranslation } from '../../Translation/Translation';
import './GlobalComment.scss';

function GlobalComment() {
    const { data, error, isLoading } = useGetGenericCommentQuery();
    const [insertGenericComment] = useInsetGenericCommentMutation();
    const currentYear = new Date().getFullYear();
    const [date, setDate] = useState(currentYear);
    // const [filteredData, setFilteredData] = useState();
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { startTyping, errorOccured, pleaseWait } = eluxTranslation;

    const postCommentField = useRef();

    const handleYearChange = (value) => {
        setDate(value);
    };

    const handleCommentInsert = async (event) => {
        event.preventDefault();
        if (postCommentField.current.value !== '') {
            const payload = {
                comment_content: postCommentField.current.value,
            };

            try {
                await insertGenericComment(payload).unwrap();
                postCommentField.current.value = '';
            } catch (e) {
                console.log('An Error Occurred', e);
            }
        } else {
            console.log('Please Enter a value');
        }
    };

    const years = [];
    for (let year = 1950; year <= currentYear; year += 1) {
        const yearObject = {
            label: year,
            value: year,
        };

        years.push(yearObject);
        console.log('Years');
    }

    return (
        <div className="global-comment-container">
            <div className="comment-filters">
                <Select
                    defaultValue={date}
                    style={{ width: 200, height: 54 }}
                    onChange={handleYearChange}
                    options={years}
                />
            </div>
            <ul className="comments">
                {error
                    ? errorOccured
                    : isLoading
                    ? pleaseWait
                    : data.data
                          .filter(
                              (comment) =>
                                  String(new Date(comment.comment_date).getFullYear()) ===
                                  String(date)
                          )
                          .map((comment) => (
                              <li key={comment.comment_ID}>
                                  {comment.comment_content}
                                  <button type="button">Delete</button>
                                  <button type="button">Edit</button>
                              </li>
                          ))}
            </ul>
            <form className="comment-submit-wrapper" onSubmit={handleCommentInsert}>
                <input type="text" placeholder={startTyping} ref={postCommentField} />
                <button type="submit">
                    <img src={assetsPath + rocketIcon} alt="rocket icon" />
                </button>
            </form>
        </div>
    );
}

export default GlobalComment;
