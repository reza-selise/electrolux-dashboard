import { Button } from 'antd';
import React from 'react';
import { useDispatch } from 'react-redux';
import { setModal } from '../../Redux/Slice/modalSlice';

function ModalButton({ location, children }) {
    // const { location, setLocation } = useState();
    const dispatch = useDispatch();
    const showModal = () => {
        dispatch(setModal());
        // setLocation(where);
    };
    console.log(location);

    return <Button onClick={showModal}>{children}</Button>;
}

export default ModalButton;
