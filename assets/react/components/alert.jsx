import * as React from "react";
import Button from "@mui/material/Button";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import Alert from "@mui/material/Alert";

export default function AlertDialog(props) {
  const handleClose = () => {
    props.onClose();
  };

  return (
    <React.Fragment>
      <Dialog
        open={true}
        onClose={handleClose}
      >
        <Alert severity="error">{props.children}</Alert>
      </Dialog>
    </React.Fragment>
  );
}
