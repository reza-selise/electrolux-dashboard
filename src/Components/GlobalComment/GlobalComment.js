import { Select } from 'antd';
import React, { useEffect, useRef, useState } from 'react';
import {
    useDeleteGenericCommentMutation,
    useGetGenericCommentQuery,
    useInsetGenericCommentMutation,
    useUpdateGenericCommentMutation
} from '../../API/apiSlice';
import deleteIcon from '../../images/delete.svg';
import pencilIcon from '../../images/pencil.svg';
import rocketIcon from '../../images/rocket.svg';
import saveIcon from '../../images/save.svg';
import { eluxTranslation } from '../../Translation/Translation';
import './GlobalComment.scss';

function GlobalComment() {
    const { data, error, isLoading } = useGetGenericCommentQuery();
    const [insertGenericComment] = useInsetGenericCommentMutation();
    const [deleteGenericComment, response] = useDeleteGenericCommentMutation();
    const [updateGenericComment] = useUpdateGenericCommentMutation();
    const currentYear = new Date().getFullYear();
    const [date, setDate] = useState(currentYear);
    const [isEdit, setIsEdit] = useState(false);
    const [commentContent, setCommentContent] = useState('');
    const [commentId, setCommentID] = useState();
    const [years, setYears] = useState([]);
    // const [filteredData, setFilteredData] = useState();
    const assetsPath = window.eluxDashboard.assetsUrl;
    const { currentUser } = window.eluxDashboard;
    const { startTyping, errorOccured, pleaseWait } = eluxTranslation;

    const postCommentField = useRef();
    const handleYearChange = value => {
        setDate(value);
    };

    const handleCommentInsert = async event => {
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

    const deleteCommentHandler = async () => {
        try {
            await deleteGenericComment({
                comment_id: commentId,
            });
            setIsEdit(!isEdit);
        } catch (e) {
            console.log('An Error Occurred', e);
        }
    };
    const updateCommentHandler = async () => {
        try {
            await updateGenericComment({
                comment_id: commentId,
                comment_content: commentContent,
            });
            setIsEdit(!isEdit);
        } catch (e) {
            console.log('An Error Occurred', e);
        }
    };
    const openCommentEditBox = event => {
        setIsEdit(!isEdit);
        setCommentID(event.target.closest('button').getAttribute('data-id'));
    };

    const updateCommentOnChange = event => {
        setCommentContent(event.target.value);
    };

    useEffect(() => {
        const comment =
            data && data.data.find(comment => String(comment.comment_ID) === String(commentId));
        try {
            setCommentContent(comment.comment_content);
        } catch (error) {
            console.log('State Not Updated', error);
        }
    }, [commentId]);

    useEffect(() => {
        const years = [];
        for (let year = 1950; year <= currentYear; year += 1) {
            const yearObject = {
                label: year,
                value: year,
            };

            years.push(yearObject);
            setYears(years);
        }
    }, []);

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
            <ul className="comments" style={isEdit ? { opacity: 0.3 } : { opacity: 1 }}>
                {error
                    ? errorOccured
                    : isLoading
                    ? pleaseWait
                    : data.data
                          .filter(
                              comment =>
                                  String(new Date(comment.comment_date).getFullYear()) ===
                                  String(date)
                          )
                          .map(comment => (
                              <li key={comment.comment_ID}>
                                  <span>{comment.comment_content}</span>
                                  {comment.user_id === currentUser ? (
                                      <div className="comment-action">
                                          <button
                                              type="button"
                                              data-id={comment.comment_ID}
                                              onClick={openCommentEditBox}
                                          >
                                              <img src={assetsPath + pencilIcon} alt="edit icon" />
                                          </button>
                                      </div>
                                  ) : (
                                      ''
                                  )}
                              </li>
                          ))}
            </ul>
            {isEdit === false ? (
                <form className="comment-submit-wrapper" onSubmit={handleCommentInsert}>
                    <input type="text" placeholder={startTyping} ref={postCommentField} />
                    <button type="submit">
                        <img src={assetsPath + rocketIcon} alt="rocket icon" />
                    </button>
                </form>
            ) : (
                <div className="update-generic-comment-wrapper">
                    <textarea value={commentContent} onChange={updateCommentOnChange} />
                    <div className="comment-action">
                        <button type="button" onClick={updateCommentHandler}>
                            <img src={assetsPath + saveIcon} alt="Save Icon" />
                        </button>
                        <button type="button" onClick={deleteCommentHandler}>
                            <img src={assetsPath + deleteIcon} alt="Delete Icon" />
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

export default GlobalComment;
