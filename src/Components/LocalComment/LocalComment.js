import React, { useEffect, useRef, useState } from 'react';
import { useSelector } from 'react-redux';
import {
    useDeleteIndividualCommentMutation,
    useGetIndividualCommentQuery,
    useInsetIndividualCommentMutation,
    // eslint-disable-next-line prettier/prettier
    useUpdateIndividualCommentMutation
} from '../../API/apiSlice';
import dotsIcon from '../../images/dots.svg';
import rocketIcon from '../../images/rocket.svg';
import saveIcon from '../../images/save.svg';
import { eluxTranslation } from '../../Translation/Translation';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import './LocalComment.scss';

function LocalComment() {
    const location = useSelector(state => state.location.value);
    const graphURL = useSelector(state => state.graphURL.value);
    const tableURL = useSelector(state => state.tableURL.value);

    const [isEdit, setIsEdit] = useState(false);
    const [showButtons, setShowButtons] = useState(null);
    const [commentContent, setCommentContent] = useState('');
    const [commentId, setCommentID] = useState();
    const [grapOrTableComment, setGrapOrTableComment] = useState('graph');

    const [insetIndividualComment] = useInsetIndividualCommentMutation();
    const [deleteIndividualComment] = useDeleteIndividualCommentMutation();
    const [updateIndividualComment] = useUpdateIndividualCommentMutation();

    const { data, error, isLoading } = useGetIndividualCommentQuery({
        section_name: location,
    });

    const assetsPath = window.eluxDashboard.assetsUrl;
    const individualCommentField = useRef();

    const { startTyping } = eluxTranslation;

    const handleCommentActionButtons = comment => {
        setShowButtons(comment);
    };

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

    const handleEdit = event => {
        setIsEdit(true);
        setCommentID(event.target.closest('button').getAttribute('data-id'));
    };
    const updateCommentHandler = async () => {
        try {
            await updateIndividualComment({
                comment_id: commentId,
                comment_content: commentContent,
            });
            setIsEdit(!isEdit);
        } catch (e) {
            console.log('An Error Occurred', e);
        }
    };

    const handleDelete = async event => {
        console.log(event.target.closest('button').getAttribute('data-id'));
        try {
            await deleteIndividualComment({
                comment_id: event.target.closest('button').getAttribute('data-id'),
            });
            // setIsEdit(!isEdit);
        } catch (e) {
            console.log('An Error Occurred', e);
        }
    };

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

    useEffect(() => {
        const comment =
            data && data.data.find(comment => String(comment.comment_ID) === String(commentId));

        try {
            setCommentContent(comment.comment_content);
        } catch (error) {
            setCommentContent('');
            // console.log('State Not Updated', error);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [commentId]);

    return (
        <div className="local-comment-wrapper">
            <div className="left-comment">
                <GraphTableSwitch
                    grapOrTable={grapOrTableComment}
                    setgGrapOrTable={setGrapOrTableComment}
                    name="local-comment"
                />
                {grapOrTableComment === 'graph' ? (
                    <GraphView graphURL={graphURL} />
                ) : (
                    <TableView tableURL={tableURL} />
                )}
            </div>
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
                                      <button
                                          type="button"
                                          onClick={() => handleCommentActionButtons(comment)}
                                      >
                                          <img src={assetsPath + dotsIcon} alt="comment icon" />
                                      </button>
                                      {showButtons === comment && (
                                          <div className="comment-action-buttons">
                                              <button
                                                  type="button"
                                                  onClick={handleDelete}
                                                  data-id={comment.comment_ID}
                                              >
                                                  Delete
                                              </button>
                                              <button
                                                  type="button"
                                                  onClick={handleEdit}
                                                  data-id={comment.comment_ID}
                                              >
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
                        <div className="comment-action">
                            <button type="button" onClick={updateCommentHandler}>
                                <img src={assetsPath + saveIcon} alt="Save Icon" />
                            </button>
                            {/* <button type="button" onClick={deleteCommentHandler}>
                                <img src={assetsPath + deleteIcon} alt="Delete Icon" />
                            </button> */}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default LocalComment;

function GraphView({ graphURL }) {
    return <img src={graphURL} alt="graph-view" />;
}

function TableView({ tableURL }) {
    // eslint-disable-next-line react/no-danger
    return <div dangerouslySetInnerHTML={{ __html: tableURL }} />;
}
