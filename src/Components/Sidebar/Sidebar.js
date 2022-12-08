import { Layout } from 'antd';
import React from 'react';

import { useDispatch, useSelector } from 'react-redux';
import logoSmall from '../../images/logo-small.svg';
import { setCollapse } from '../../Redux/Slice/collapseSlice';
import CustomMenu from '../CustomMenu/CustomMenu';
import Logo from '../Logo/Logo';
import './Sidebar.scss';

const { Sider } = Layout;

function Sidebar() {
    // const [collapsed, setCollapsed] = useState(false);
    const collapsed = useSelector((state) => state.collapse.value);

    const dispatch = useDispatch();

    const handleCollapsed = () => {
        dispatch(setCollapse());
    };

    return (
        <Sider
            collapsible
            collapsed={collapsed}
            collapsedWidth={40}
            width={292}
            onCollapse={handleCollapsed}
            style={{
                overflow: 'auto',
                height: '100vh',
                position: 'fixed',
                left: 0,
                top: 0,
                bottom: 0,
                zIndex: 2,
                padding: collapsed ? 8 : 25,
            }}
            className="site-sidebar"
        >
            {collapsed ? <img className="small-logo" src={logoSmall} alt="logo small" /> : <Logo />}

            <CustomMenu />
        </Sider>
    );
}

export default Sidebar;
