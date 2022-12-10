import { Modal } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setModal } from '../../Redux/Slice/modalSlice';
import GlobalComment from '../GlobalComment/GlobalComment';

function CustomModal() {
    const isModalOpen = useSelector((state) => state.modal.value);
    const dispatch = useDispatch();
    const handleCancel = () => {
        dispatch(setModal());
    };
    const location = 'global-comment';

    switch (location) {
        case 'global-comment':
            return (
                <Modal title="Comments" open={isModalOpen} onCancel={handleCancel} footer={null}>
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
