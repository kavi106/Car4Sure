import * as React from "react";
import AppBar from "@mui/material/AppBar";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import Box from "@mui/material/Box";
import IconButton from "@mui/material/IconButton";
import LogoutOutlinedIcon from "@mui/icons-material/LogoutOutlined";

export default function Header(props) {
  return (
    <AppBar
      position="absolute"
      color="default"
      elevation={0}
      sx={{
        position: "relative",
        borderBottom: (t) => `1px solid ${t.palette.divider}`,
      }}
    >
      <Toolbar>
        <Typography variant="h5" color="black" noWrap component="div">
          Car4Sure
        </Typography>
        <Box sx={{ flexGrow: 1 }} />
        <Typography color="inherit" noWrap component="div">
          {props.username !== "" && `You are logged in as ${props.username}`}
        </Typography>
        <IconButton
          size="small"
          aria-label="account of current user"
          aria-controls="menu-appbar"
          aria-haspopup="true"
          color="primary"
          sx={{marginLeft: 3}}
          onClick={props.onLogout}
        >
          <LogoutOutlinedIcon />
        </IconButton>
      </Toolbar>
    </AppBar>
  );
}
