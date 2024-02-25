import * as React from "react";
import Dialog from "@mui/material/Dialog";
import DialogContent from "@mui/material/DialogContent";
import DialogActions from "@mui/material/DialogActions";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import CssBaseline from "@mui/material/CssBaseline";
import Link from "@mui/material/Link";
import Grid from "@mui/material/Grid";
import Box from "@mui/material/Box";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Typography from "@mui/material/Typography";
import Container from "@mui/material/Container";
import Spinner from "./spinner";
import { callApi } from "../callApi";
import Stack from "@mui/material/Stack";
import { DataGrid } from "@mui/x-data-grid";
import AddOutlinedIcon from "@mui/icons-material/AddOutlined";
import TextField from "@mui/material/TextField";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { DateField } from "@mui/x-date-pickers/DateField";
import dayjs from "dayjs";
import MenuItem from "@mui/material/MenuItem";
import IconButton from "@mui/material/IconButton";
import DeleteForeverOutlinedIcon from "@mui/icons-material/DeleteForeverOutlined";
import EditOutlinedIcon from "@mui/icons-material/EditOutlined";
import AlertDialog from "./alert";

const defaultDriverData = { id: -1 };
const defaultAlert = { code: "", message: "" };

export default function DriversDialog(props) {
  const [rows, setRows] = React.useState([]);
  const [refreshPage, setRefreshPage] = React.useState(null);
  const [spinner, setSpinner] = React.useState(false);
  const [driverData, setDriverData] = React.useState(defaultDriverData);
  const [error, setError] = React.useState(defaultAlert);

  React.useEffect(() => {
    setSpinner(true);
    callApi("GET", `/api/drivers/${props.policy}`, {}, props.token).then(
      (response) => {
        if (response?.length >= 0) {
          setRows(response);
        } else if (response.code) {
          props.onLogout();
        }
        setSpinner(false);
        // console.log(response);
      }
    );
  }, [refreshPage]);

  const handleClose = () => {
    props.onClose(-1);
  };

  const handleDeleteDriver = (id) => {
    setSpinner(true);
    callApi(
      "DELETE",
      `/api/driver/${props.policy}/${id}`,
      {},
      props.token
    ).then((response) => {
      if (response.code == 401) props.onLogout();
      if (response.code == 200) {
        setError(response);
        setRefreshPage(Math.random());
      }
      setSpinner(false);
    });
  };

  const closeAlertHandler = () => {
    setError(defaultAlert);
  };

  const columns = [
    {
      field: "id",
      headerName: "ID",
      width: 20,
      valueFormatter: (params) => {
        return params.value;
      },
    },
    {
      field: "name",
      headerName: "Name",
      width: 200,
      valueGetter: (params) =>
        `${params.row.firstName || ""} ${params.row.lastName || ""}`,
    },
    {
      field: "dob",
      headerName: "DOB",
      width: 100,
      valueFormatter: (params) =>
        new Date(params.value.toString()).toLocaleDateString("en-CA"),
    },
    {
      field: "gender",
      headerName: "Gender",
      width: 100,
    },
    {
      field: "maritalStatus",
      headerName: "Marital Statue",
      width: 100,
    },
    {
      field: "licenseNumber",
      headerName: "License Number",
      width: 100,
    },
    {
      field: "licenseState",
      headerName: "License State",
      width: 100,
    },
    {
      field: "licenseStatus",
      headerName: "License Status",
      width: 100,
    },
    {
      field: "licenseEffectiveDate",
      headerName: "License Effective Date",
      width: 100,
      valueFormatter: (params) =>
        new Date(params.value?.toString()).toLocaleDateString("en-CA"),
    },
    {
      field: "licenseExpirationDate",
      headerName: "License Expiration Date",
      width: 100,
      valueFormatter: (params) =>
        new Date(params.value?.toString()).toLocaleDateString("en-CA"),
    },
    {
      field: "licenseClass",
      headerName: "License Class",
      width: 100,
    },
    {
      field: "deleteIcon",
      headerName: "",
      description: "Actions column.",
      sortable: false,
      width: 50,
      renderCell: (params) => {
        return (
          <IconButton
            size="small"
            color="error"
            onClick={() => {
              handleDeleteDriver(params.row.id);
            }}
          >
            <DeleteForeverOutlinedIcon />
          </IconButton>
        );
      },
    },
    {
      field: "editIcon",
      headerName: "",
      description: "Actions column.",
      sortable: false,
      width: 50,
      renderCell: (params) => {
        return (
          <IconButton
            size="small"
            color="primary"
            onClick={() => {
              setDriverData(params.row);
            }}
          >
            <EditOutlinedIcon />
          </IconButton>
        );
      },
    },
  ];

  const title =
    driverData?.id == 0
      ? "Add Driver"
      : `Edit Driver ${driverData.name} linked to policy ${props.policy
          .toString()
          .padStart(10, "0")}`;

  const submitFormHandler = (event) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    const formJson = Object.fromEntries(formData.entries());

    setSpinner(true);
    callApi(
      "POST",
      `/api/driver`,
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
        setDriverData(defaultDriverData);
        setRefreshPage(Math.random());
      }
      if (response.code && response.code != 401) {
        setError(response);
      }
      setSpinner(false);
    });
  };

  return (
    <React.Fragment>
      {spinner && <Spinner />}
      {error.code !== "" && error.message !== "" && (
        <AlertDialog onClose={closeAlertHandler}>{error.message}</AlertDialog>
      )}
      {driverData?.id < 0 && (
        <Dialog
          open={true}
          maxWidth="xl"
          fullWidth={true}
        >
          <DialogContent>
            <Container component="main" maxWidth="xl">
              <Box alignItems="center">
                <Typography component="h1" variant="h5">
                  Drivers linked to policy{" "}
                  {props.policy.toString().padStart(10, "0")}
                </Typography>
                <Grid container>
                  <Grid item xs></Grid>
                  <Grid item>
                    <Button
                      disableRipple
                      size="small"
                      variant="contained"
                      startIcon={<AddOutlinedIcon />}
                      onClick={() => {
                        setDriverData({ ...defaultDriverData, id: 0 });
                      }}
                    >
                      Driver
                    </Button>
                  </Grid>
                </Grid>

                <DataGrid
                  rows={rows}
                  columns={columns}
                  initialState={{
                    pagination: {
                      paginationModel: { page: 0, pageSize: 10 },
                    },
                  }}
                  pageSizeOptions={[5, 10]}
                />
              </Box>
            </Container>
            <DialogActions>
              <Button variant="outlined" onClick={handleClose}>
                Close
              </Button>
            </DialogActions>
          </DialogContent>
        </Dialog>
      )}
      {driverData?.id >= 0 && (
        <Dialog
          open={true}
          maxWidth="sm"
          fullWidth={true}
          PaperProps={{
            component: "form",
            onSubmit: submitFormHandler,
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
                  value={driverData?.id}
                />
                <TextField
                  sx={{ visibility: "hidden" }}
                  type="hidden"
                  name="policyId"
                  value={props.policy}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="firstName"
                  label="First Name"
                  name="firstName"
                  autoFocus
                  defaultValue={driverData?.firstName}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="lastName"
                  label="Last Name"
                  name="lastName"
                  autoFocus
                  defaultValue={driverData?.lastName}
                />
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DateField
                    margin="normal"
                    required
                    id="dateOfBirth"
                    name="dateOfBirth"
                    label="Date of birth"
                    fullWidth
                    format="YYYY-MM-DD"
                    defaultValue={
                      driverData.id > 0 ? dayjs(driverData?.dateOfBirth) : null
                    }
                  />
                </LocalizationProvider>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="gender"
                  name="gender"
                  select
                  label="Gender"
                  defaultValue={driverData?.gender ?? ""}
                >
                  <MenuItem value="Male">Male</MenuItem>
                  <MenuItem value="Female">Female</MenuItem>
                  <MenuItem value="Other">Other</MenuItem>
                </TextField>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="maritalStatus"
                  name="maritalStatus"
                  select
                  label="Marital Statue"
                  defaultValue={driverData?.maritalStatus ?? ""}
                >
                  <MenuItem value="Married">Married</MenuItem>
                  <MenuItem value="Single">Single</MenuItem>
                  <MenuItem value="Divorced">Divorced</MenuItem>
                  <MenuItem value="Widowed">Widowed</MenuItem>
                </TextField>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="licenseNumber"
                  label="License Number"
                  name="licenseNumber"
                  type="number"
                  defaultValue={driverData?.licenseNumber}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="licenseState"
                  label="License State"
                  name="licenseState"
                  defaultValue={driverData?.licenseState}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="licenseStatus"
                  label="License Status"
                  name="licenseStatus"
                  select
                  defaultValue={driverData?.licenseStatus ?? ""}
                >
                  <MenuItem value="Valid">Valid</MenuItem>
                  <MenuItem value="Invalid">Invalid</MenuItem>
                </TextField>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DateField
                    margin="normal"
                    required
                    id="licenseEffectiveDate"
                    name="licenseEffectiveDate"
                    label="License Effective Date"
                    fullWidth
                    format="YYYY-MM-DD"
                    defaultValue={
                      driverData.id > 0
                        ? dayjs(driverData?.licenseEffectiveDate)
                        : null
                    }
                  />
                </LocalizationProvider>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DateField
                    margin="normal"
                    required
                    id="licenseExpirationDate"
                    name="licenseExpirationDate"
                    label="License Expiration Date"
                    fullWidth
                    format="YYYY-MM-DD"
                    defaultValue={
                      driverData.id > 0
                        ? dayjs(driverData?.licenseExpirationDate)
                        : null
                    }
                  />
                </LocalizationProvider>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="licenseClass"
                  label="License Class"
                  name="licenseClass"
                  defaultValue={driverData?.licenseClass}
                />
              </Box>
            </Container>
          </DialogContent>
          <DialogActions>
            <Button
              variant="outlined"
              onClick={() => {
                setDriverData(defaultDriverData);
              }}
            >
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
