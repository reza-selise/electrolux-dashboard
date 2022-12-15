import { Modal } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setModal } from '../../Redux/Slice/modalSlice';
import { eluxTranslation } from '../../Translation/Translation';
import GlobalComment from '../GlobalComment/GlobalComment';
import './CustomModal.scss';

function CustomModal() {
    const isModalOpen = useSelector((state) => state.modal.value);
    const location = useSelector((state) => state.location.value);
    const { genericComments, timeline } = eluxTranslation;
    const dispatch = useDispatch();
    const handleCancel = () => {
        dispatch(setModal());
    };
    console.log(location);
    // const location = 'global-comment';
    // const location = 'event-by-year';

    switch (location) {
        case 'global-comment':
            return (
                <Modal
                    title={genericComments}
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                    width={991}
                    className="global-comment-modal"
                >
                    <GlobalComment />
                </Modal>
            );
        case 'event-by-year':
            return (
                <Modal
                    title={timeline}
                    open={isModalOpen}
                    onCancel={handleCancel}
                    footer={null}
                    width={991}
                    className="timeline-modal"
                >
                    Hello Timeline
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
