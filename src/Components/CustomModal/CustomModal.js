import { Modal } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setModal } from '../../Redux/Slice/modalSlice';
import { eluxTranslation } from '../../Translation/Translation';
import GlobalComment from '../GlobalComment/GlobalComment';
import './CustomModal.scss';

function CustomModal() {
    const isModalOpen = useSelector((state) => state.modal.value);
    const { genericComments } = eluxTranslation;
    const dispatch = useDispatch();
    const handleCancel = () => {
        dispatch(setModal());
    };
    const location = 'global-comment';

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
