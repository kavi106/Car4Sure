import * as React from 'react';
import Backdrop from '@mui/material/Backdrop';
import CircularProgress from '@mui/material/CircularProgress';

export default function Spinner() {
  return (
    <div>
      <Backdrop
        sx={{ color: '#fff', zIndex: 2000 }}
        open={true}
      >
        <CircularProgress color="inherit" />
      </Backdrop>
    </div>
  );
}
