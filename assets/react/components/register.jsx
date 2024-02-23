import * as React from "react";
import Dialog from "@mui/material/Dialog";
import DialogContent from "@mui/material/DialogContent";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Link from "@mui/material/Link";
import Grid from "@mui/material/Grid";
import Box from "@mui/material/Box";
import AppRegistrationOutlinedIcon from '@mui/icons-material/AppRegistrationOutlined';
import Typography from "@mui/material/Typography";
import Container from "@mui/material/Container";

export default function RegisterDialog(props) {
    const [passwordMatch, setPasswordMatch] = React.useState(true);
    const [passwordMisMatchMessage, setPasswordMisMatchMessage] = React.useState("");

  return (
    <React.Fragment>
      <Dialog
        open={true}
        maxWidth="sm"
        fullWidth={true}
        PaperProps={{
          component: "form",
          onSubmit: (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const formJson = Object.fromEntries(formData.entries());
            const username = formJson.username;
            if (formJson.password !== formJson.confirm_password) {
                setPasswordMatch(false);
                setPasswordMisMatchMessage("Confirm password does not match.");
            } else if (formJson.username && formJson.password) {
                props.onRegister(formJson.username, formJson.password);
            }
          },
        }}
      >
        <DialogContent>
          <Container component="main">
            <CssBaseline />
            <Box
              sx={{
                marginTop: 1,
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
              }}
            >
              <Avatar sx={{ m: 1, bgcolor: "secondary.main" }}>
                <AppRegistrationOutlinedIcon />
              </Avatar>
              <Typography component="h1" variant="h5">
                Register
              </Typography>
              <TextField
                margin="normal"
                required
                fullWidth
                id="username"
                label="Username"
                name="username"
                autoComplete="username"
                autoFocus
              />
              <TextField
                margin="normal"
                required
                fullWidth
                name="password"
                label="Password"
                type="password"
                id="password"
                autoComplete="current-password"
              />
              <TextField
                error={!passwordMatch}
                margin="normal"
                required
                fullWidth
                name="confirm_password"
                label="Confirm Password"
                type="password"
                id="confirm_password"
                autoComplete="confirm-password"
                helperText={passwordMisMatchMessage}
              />
              <Button
                type="submit"
                fullWidth
                variant="contained"
                sx={{ mt: 3, mb: 2 }}
              >
                Register
              </Button>
              <Grid container>
                <Grid item xs></Grid>
                <Grid item>
                  <Link
                    href="#"
                    variant="body2"
                    onClick={() => {
                      props.onRegister(false);
                    }}
                  >
                    {"Already have an account? Sign in"}
                  </Link>
                </Grid>
              </Grid>
            </Box>
          </Container>
        </DialogContent>
      </Dialog>
    </React.Fragment>
  );
}
