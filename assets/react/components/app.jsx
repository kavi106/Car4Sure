import * as React from "react";
import CssBaseline from "@mui/material/CssBaseline";
import Typography from "@mui/material/Typography";
import Container from "@mui/material/Container";
import Paper from "@mui/material/Paper";
import LoginDialog from "./login";
import RegisterDialog from "./register";
import AlertDialog from "./alert";
import { callApi } from "../callApi";
import Cookies from "js-cookie";
import Header from "./header";

const defaultAlert = { code: "", message: "" };

const App = () => {
  const [token, setToken] = React.useState(null);
  const [username, setUsername] = React.useState("");
  const [register, setRegister] = React.useState(false);
  const [error, setError] = React.useState(defaultAlert);

  React.useEffect(() => {
    const jwtToken = Cookies.get("jwt");
    const storedUsername = Cookies.get("username");
    if (jwtToken) setToken(jwtToken);
    if (storedUsername) setUsername(storedUsername);
  }, []);

  const loginHandler = (username, password) => {
    callApi(
      "POST",
      `/api/login_check`,
      { username: username, password: password },
      "",
      [{ name: "Content-Type", value: "application/json" }]
    ).then((response) => {
      if (response.token) {
        setToken(response.token);
        setUsername(username);
        Cookies.set("jwt", response.token, { expires: 7, secure: true });
        Cookies.set("username", username, { expires: 7, secure: true });
      } else if (response.code) {
        setError(response);
      }
    });
  };

  const logOutHandler = () => {
    setToken(null);
    setUsername("");
    Cookies.remove("jwt");
  };

  const registerHandler = (username, password) => {
    callApi(
      "POST",
      `/api/user`,
      { username: username, password: password },
      "",
      [{ name: "Content-Type", value: "application/x-www-form-urlencoded;charset=UTF-8" }],
      'urlencoded'
    ).then((response) => {
      if (response.username == username) {
        setRegister(false);
      } else if (response.code) {
        setError(response);
      }
    });
  };

  const closeAlertHandler = () => {
    setError(defaultAlert);
  };
  return (
    <React.Fragment>
      {error.code !== "" && error.message !== "" && (
        <AlertDialog onClose={closeAlertHandler}>{error.message}</AlertDialog>
      )}
      {!token && !register && (
        <LoginDialog onRegister={setRegister} onLogin={loginHandler} />
      )}
      {register && <RegisterDialog onRegister={registerHandler} />}

      <CssBaseline />
      <Header username={username} onLogout={logOutHandler} />
      <Container component="main" maxWidth="sm" sx={{ mb: 4 }}>
        <Paper
          variant="outlined"
          sx={{ my: { xs: 3, md: 6 }, p: { xs: 2, md: 3 } }}
        >
          <Typography component="h2" variant="body" align="center">
            You're Logged In
          </Typography>
        </Paper>
      </Container>
    </React.Fragment>
  );
};

export default App;