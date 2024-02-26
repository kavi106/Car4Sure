import * as React from "react";
import Dialog from "@mui/material/Dialog";
import DialogContent from "@mui/material/DialogContent";
import DialogActions from "@mui/material/DialogActions";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import TextField from "@mui/material/TextField";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Container from "@mui/material/Container";
import MenuItem from "@mui/material/MenuItem";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { DateField } from "@mui/x-date-pickers/DateField";
import dayjs from "dayjs";
import { callApi } from "../callApi";
import Spinner from "./spinner";
import AlertDialog from "./alert";

const defaultAlert = { code: "", message: "" };

export default function PolicyDialog(props) {
  const [spinner, setSpinner] = React.useState(false);
  const [error, setError] = React.useState(defaultAlert);

  const handleClose = () => {
    props.onShowPolicy(null);
  };

  const submitHandler = (event) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    const formJson = Object.fromEntries(formData.entries());

    setSpinner(true);
    callApi(
      "POST",
      `/api/policy`,
      formJson,
      props.token,
      [
        {
          name: "Content-Type",
          value: "application/x-www-form-urlencoded;charset=UTF-8",
        },
      ],
      "urlencoded"
    ).then((response) => {
      if (response.code == 401) props.onLogout();
      if (response.id > 0) {
        handleClose();
        props.onRefreshPolicies(Math.random());
      }
      if (response.code && response.code != 401) {
        setError(response);
      }
      setSpinner(false);
    });
  };

  const title =
    props.policyData?.id > 0
      ? `Edit Policy ${props.policyData?.id.toString().padStart(10, "0")}`
      : "Create a new policy";
  return (
    <React.Fragment>
      {spinner && <Spinner />}
      {error.code !== "" && error.message !== "" && (
        <AlertDialog onClose={handleClose}>{error.message}</AlertDialog>
      )}
      {error.code == "" && error.message == "" && (
        <Dialog
          open={true}
          maxWidth="sm"
          fullWidth={true}
          PaperProps={{
            component: "form",
            onSubmit: submitHandler,
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
                }}
              >
                <Typography
                  component="h1"
                  variant="h5"
                  sx={{ alignSelf: "center" }}
                >
                  {title}
                </Typography>
                <TextField
                  sx={{ visibility: "hidden" }}
                  type="hidden"
                  name="id"
                  value={props.policyData?.id}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="policyStatus"
                  name="policyStatus"
                  select
                  defaultValue={props.policyData.policyStatus ?? "Active"}
                  label="Policy Status"
                >
                  <MenuItem value="Active">Active</MenuItem>
                  <MenuItem value="Inactive">Inactive</MenuItem>
                </TextField>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="policyType"
                  name="policyType"
                  select
                  defaultValue={props.policyData.policyType ?? "Auto"}
                  label="Policy Type"
                >
                  <MenuItem value="Auto">Auto</MenuItem>
                </TextField>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DateField
                    margin="normal"
                    required
                    id="policyEffectiveDate"
                    name="policyEffectiveDate"
                    label="Policy Effective Date"
                    fullWidth
                    format="YYYY-MM-DD"
                    defaultValue={
                      props.policyData.id > 0
                        ? dayjs(props.policyData.policyEffectiveDate)
                        : null
                    }
                  />
                </LocalizationProvider>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DateField
                    margin="normal"
                    required
                    id="policyExpirationDate"
                    name="policyExpirationDate"
                    label="Policy Expiration Date"
                    fullWidth
                    format="YYYY-MM-DD"
                    defaultValue={
                      props.policyData.id > 0
                        ? dayjs(props.policyData.policyExpirationDate)
                        : null
                    }
                  />
                </LocalizationProvider>
                <Typography
                  variant="subtitle1"
                  display="block"
                  sx={{ marginTop: 1 }}
                >
                  Policy Holder
                </Typography>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="firstName"
                  label="First Name"
                  name="firstName"
                  defaultValue={props.policyData?.policyHolder?.firstName ?? ""}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="lastName"
                  label="Last Name"
                  name="lastName"
                  defaultValue={props.policyData?.policyHolder?.lastName ?? ""}
                />
                <Typography
                  variant="subtitle1"
                  display="block"
                  sx={{ marginTop: 1 }}
                >
                  Policy Holder Address
                </Typography>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="street"
                  label="Street"
                  name="street"
                  defaultValue={
                    props.policyData?.policyHolder?.address?.street ?? ""
                  }
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="city"
                  label="City"
                  name="city"
                  defaultValue={
                    props.policyData?.policyHolder?.address?.city ?? ""
                  }
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="state"
                  label="State"
                  name="state"
                  defaultValue={
                    props.policyData?.policyHolder?.address?.state ?? ""
                  }
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="zip"
                  label="Zip"
                  name="zip"
                  type="number"
                  defaultValue={
                    props.policyData?.policyHolder?.address?.zip ?? ""
                  }
                />
              </Box>
            </Container>
          </DialogContent>
          <DialogActions>
            <Button variant="outlined" onClick={handleClose}>
              Close
            </Button>
            <Button type="submit" variant="contained" autoFocus>
              Save
            </Button>
          </DialogActions>
        </Dialog>
      )}
    </React.Fragment>
  );
}
