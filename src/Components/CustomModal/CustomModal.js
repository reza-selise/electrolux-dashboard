import { Modal } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setModal } from '../../Redux/Slice/modalSlice';
import { eluxTranslation } from '../../Translation/Translation';
import GlobalComment from '../GlobalComment/GlobalComment';
import LocalComment from '../LocalComment/LocalComment';
import Timeline from '../Timeline/Timeline';
import './CustomModal.scss';

function CustomModal() {
    const isModalOpen = useSelector(state => state.modal.value);
    const location = useSelector(state => state.location.value);
    const { genericComments, timeline } = eluxTranslation;
    const dispatch = useDispatch();
    const handleCancel = () => {
        dispatch(setModal());
    };

    switch (location) {
        case 'global-comment':
            return (
                <Modal
                    title={genericComments}
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                    width={991}
                    centered
                    className="global-comment-modal"
                >
                    <GlobalComment />
                </Modal>
            );
        case 'event-by-year-timeline':
        case 'event-per-sales-person-timeline':
        case 'event-by-location-timeline':
        case 'event-by-months-timeline':
        case 'event-by-category-timeline':
        case 'event-by-cancellation-timeline':
        case 'cooking-course-type-timeline':
        case 'event-by-status-timeline':
            return (
                <Modal
                    title={timeline}
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                    width={461}
                    centered
                    className="timeline-modal"
                >
                    <Timeline />
                </Modal>
            );
        case 'event-by-year-comment':
        case 'event-by-location-comment':
        case 'event-by-month-comment':
        case 'event-by-category-comment':
        case 'event-by-status-comment':
        case 'cooking-course-comment':
        case 'sales-per-person-comment':
        case 'location="cooking-course-comment"':
            return (
                <Modal
                    title={null}
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                    width={1056}
                    centered
                    className="local-comment-modal"
                >
                    <LocalComment />
                </Modal>
            );

        default:
            return (
                <Modal
                    title="Nothing found"
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                />
            );
    }
}

export default CustomModal;
