import React, { useRef, useState } from 'react';
import { useSelector } from 'react-redux';
import {
    useGetIndividualCommentQuery,
    // eslint-disable-next-line prettier/prettier
    useInsetIndividualCommentMutation
} from '../../API/apiSlice';
import dotsIcon from '../../images/dots.svg';
import rocketIcon from '../../images/rocket.svg';
import { eluxTranslation } from '../../Translation/Translation';
import './LocalComment.scss';

function LocalComment() {
    const location = useSelector(state => state.location.value);

    // const [showButtons, setShowButtons] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const [showButtons, setShowButtons] = useState(false);
    const [commentContent, setCommentContent] = useState('');

    const handleCommentActionButtons = () => {
        setShowButtons(!showButtons);
    };

    const [insetIndividualComment] = useInsetIndividualCommentMutation();

    const { data, error, isLoading } = useGetIndividualCommentQuery({
        section_name: location,
    });

    const assetsPath = window.eluxDashboard.assetsUrl;
    const individualCommentField = useRef();

    const { startTyping } = eluxTranslation;

    const handleCommentInsert = async event => {
        event.preventDefault();
        if (individualCommentField.current.value !== '') {
            const payload = {
                comment_content: individualCommentField.current.value,
                comment_section: location,
            };

            try {
                await insetIndividualComment(payload).unwrap();
                individualCommentField.current.value = '';
            } catch (e) {
                console.log('An Error Occurred', e);
            }
        } else {
            console.log('Please Enter a value');
        }
    };

    const handleEdit = () => {
        setIsEdit(true);
    };
    // const updateCommentHandler = async () => {
    //     try {
    //         await updateGenericComment({
    //             comment_id: commentId,
    //             comment_content: commentContent,
    //         });
    //         setIsEdit(!isEdit);
    //     } catch (e) {
    //         console.log('An Error Occurred', e);
    //     }
    // };
    const updateCommentOnChange = event => {
        setCommentContent(event.target.value);
    };
    const convertDateToTimeAgo = dateString => {
        const commentDate = new Date(dateString);
        const currentDate = new Date();
        const timeDifferenceInMinutes = Math.floor((currentDate - commentDate) / 1000 / 60);

        if (timeDifferenceInMinutes < 60) {
            return `${timeDifferenceInMinutes} minutes ago`;
        }
        if (timeDifferenceInMinutes < 1440) {
            return `${Math.floor(timeDifferenceInMinutes / 60)} hrs ago`;
        }
        return `${Math.floor(timeDifferenceInMinutes / 1440)} days ago`;
    };

    return (
        <div className="local-comment-wrapper">
            <div className="left-comment" />
            <div className="right-comment">
                <h2>Comments</h2>
                <ul className="comment-list">
                    {error
                        ? 'error'
                        : isLoading
                        ? 'Loading'
                        : data &&
                          data.data.map((comment, index) => (
                              // eslint-disable-next-line react-hooks/rules-of-hooks
                              //   const [showButtons, setShowButtons] = useState(false);
                              //   const handleCommentActionButtons = () => {
                              //       setShowButtons(!showButtons);
                              //   };
                              <li key={index}>
                                  <h3>{comment.comment_author}</h3>
                                  <p>{comment.comment_content}</p>
                                  <span>{convertDateToTimeAgo(comment.comment_date)}</span>
                                  <div className="local-comment-action">
                                      <button type="button" onClick={handleCommentActionButtons}>
                                          <img src={assetsPath + dotsIcon} alt="comment icon" />
                                      </button>
                                      {showButtons && (
                                          <div className="comment-action-buttons">
                                              <button type="button">Delete</button>
                                              <button type="button" onClick={handleEdit}>
                                                  Edit
                                              </button>
                                          </div>
                                      )}
                                  </div>
                              </li>
                          ))}
                </ul>
                {isEdit === false ? (
                    <form className="comment-submit-wrapper" onSubmit={handleCommentInsert}>
                        <input type="text" placeholder={startTyping} ref={individualCommentField} />
                        <button type="submit">
                            <img src={assetsPath + rocketIcon} alt="rocket icon" />
                        </button>
                    </form>
                ) : (
                    <div className="update-generic-comment-wrapper">
                        <textarea value={commentContent} onChange={updateCommentOnChange} />
                        {/* <div className="comment-action">
                            <button type="button" onClick={updateCommentHandler}>
                                <img src={assetsPath + saveIcon} alt="Save Icon" />
                            </button>
                            <button type="button" onClick={deleteCommentHandler}>
                                <img src={assetsPath + deleteIcon} alt="Delete Icon" />
                            </button>
                        </div> */}
                    </div>
                )}
            </div>
        </div>
    );
}

export default LocalComment;
