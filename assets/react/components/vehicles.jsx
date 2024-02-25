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
import CarCrashIcon from "@mui/icons-material/CarCrash";

const defaultVehicleData = { id: -1 };
const defaultCoverageData = { id: -1 };
const defaultAlert = { code: "", message: "" };

export default function VehiclesDialog(props) {
  const [rows, setRows] = React.useState([]);
  const [rowsCoverage, setRowsCoverage] = React.useState(null);
  const [refreshPage, setRefreshPage] = React.useState(null);
  const [spinner, setSpinner] = React.useState(false);
  const [vehicleData, setVehicleData] = React.useState(defaultVehicleData);
  const [coverageData, setCoverageData] = React.useState(defaultCoverageData);
  const [error, setError] = React.useState(defaultAlert);
  const [vehicleId, setVehicleId] = React.useState(0);

  React.useEffect(() => {
    setSpinner(true);
    callApi("GET", `/api/vehicles/${props.policy}`, {}, props.token).then(
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

  const handleCloseCoverage = () => {
    setRowsCoverage(null);
  };

  const coverageDialogHandler = (id) => {
    setSpinner(true);
    callApi(
      "GET",
      `/api/coverages/${props.policy}/${id}`,
      {},
      props.token
    ).then((response) => {
      if (response?.length >= 0) {
        setRowsCoverage(response);
        setVehicleId(id);
      } else if (response.code) {
        props.onLogout();
      }
      setSpinner(false);
      // console.log(response);
    });
  };

  const handleDeleteVehicle = (id) => {
    setSpinner(true);
    callApi(
      "DELETE",
      `/api/vehicle/${props.policy}/${id}`,
      {},
      props.token
    ).then((response) => {
      if (response.code == 401) props.onLogout();
      if (response.code == 200) {
        setError(response);
        setRefreshPage(Math.random());
      }
      if (response.code > 401) setError(response);
      setSpinner(false);
    });
  };

  const handleDeleteCoverage = (id) => {
    setSpinner(true);
    callApi(
      "DELETE",
      `/api/coverage/${props.policy}/${vehicleId}/${id}`,
      {},
      props.token
    ).then((response) => {
      if (response.code == 401) props.onLogout();
      if (response.code == 200) {
        setTimeout(setError, 1000, response);
        setRowsCoverage(null);
        coverageDialogHandler(vehicleId);
      }
      if (response.code > 401) setError(response);
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
    },
    {
      field: "year",
      headerName: "Year",
      width: 70,
    },
    {
      field: "make",
      headerName: "Make",
      width: 70,
    },
    {
      field: "model",
      headerName: "Model",
      width: 70,
    },
    {
      field: "vin",
      headerName: "Vin",
      width: 120,
    },
    {
      field: "usage",
      headerName: "Usage",
      width: 100,
    },
    {
      field: "primaryUse",
      headerName: "Primary Use",
      width: 100,
    },
    {
      field: "annualMileage",
      headerName: "Annual Mileage",
      width: 110,
    },
    {
      field: "ownership",
      headerName: "Ownership",
      width: 100,
    },
    {
      field: "address",
      headerName: "Garaging Address",
      width: 270,
      valueGetter: (params) =>
        `${params.row.garagingAddress.street || ""}, ${
          params.row.garagingAddress.city || ""
        }, ${params.row.garagingAddress.state || ""}, ${
          params.row.garagingAddress.zip || ""
        }`,
    },
    {
      field: "coverage",
      headerName: "Coverage",
      description: "Actions column.",
      sortable: false,
      width: 150,
      renderCell: (params) => {
        return (
          <Button
            variant="outlined"
            startIcon={<CarCrashIcon />}
            onClick={() => {
              coverageDialogHandler(params.row.id);
            }}
          >
            Coverage
          </Button>
        );
      },
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
              handleDeleteVehicle(params.row.id);
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
              setVehicleData(params.row);
            }}
          >
            <EditOutlinedIcon />
          </IconButton>
        );
      },
    },
  ];

  const columnsCoverage = [
    {
      field: "id",
      headerName: "ID",
      width: 20,
    },
    {
      field: "type",
      headerName: "Type",
      width: 200,
    },
    {
      field: "limit",
      headerName: "Limit",
      width: 150,
    },
    {
      field: "deductible",
      headerName: "Deductible",
      width: 150,
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
                handleDeleteCoverage(params.row.id);
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
                setCoverageData(params.row);
            }}
          >
            <EditOutlinedIcon />
          </IconButton>
        );
      },
    },
  ];

  const title =
    vehicleData?.id == 0
      ? "Add Vehicle"
      : `Edit Vehicle Id ${vehicleData.id} linked to policy ${props.policy
          .toString()
          .padStart(10, "0")}`;

  const titleCoverage =
    coverageData?.id == 0
      ? "Add Coverage"
      : `Edit Coverage Id ${coverageData.id} linked to vehicle ${vehicleId}`;

  const submitVehicleFormHandler = (event) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    const formJson = Object.fromEntries(formData.entries());

    // console.log(formJson);
    setSpinner(true);
    callApi(
      "POST",
      `/api/vehicle`,
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
        setVehicleData(defaultVehicleData);
        setRefreshPage(Math.random());
      }
      if (response.code && response.code != 401) {
        setError(response);
      }
      setSpinner(false);
    });
  };

  const submitCoverageFormHandler = (event) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    const formJson = Object.fromEntries(formData.entries());

    setSpinner(true);
    callApi(
      "POST",
      `/api/coverage`,
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
        setCoverageData(defaultVehicleData);
        setRowsCoverage(null);
        coverageDialogHandler(vehicleId);
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
      {vehicleData?.id < 0 && (
        <Dialog open={true} maxWidth="xl" fullWidth={true}>
          <DialogContent>
            <Container component="main" maxWidth="xl">
              <Box alignItems="center">
                <Typography component="h1" variant="h5">
                  Vehicles linked to policy{" "}
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
                        setVehicleData({ ...defaultVehicleData, id: 0 });
                      }}
                    >
                      Vehicle
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
      {rowsCoverage != null && rowsCoverage.length >= 0 && (
        <Dialog open={true} maxWidth="md" fullWidth={true}>
          <DialogContent>
            <Container component="main" maxWidth="xl">
              <Box alignItems="center">
                <Typography component="h1" variant="h5">
                  Coverage linked to vehicle {vehicleId}
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
                        setCoverageData({ ...defaultCoverageData, id: 0 });
                      }}
                    >
                      Coverage
                    </Button>
                  </Grid>
                </Grid>

                <DataGrid
                  rows={rowsCoverage}
                  columns={columnsCoverage}
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
              <Button variant="outlined" onClick={handleCloseCoverage}>
                Close
              </Button>
            </DialogActions>
          </DialogContent>
        </Dialog>
      )}
      {vehicleData?.id >= 0 && (
        <Dialog
          open={true}
          maxWidth="sm"
          fullWidth={true}
          PaperProps={{
            component: "form",
            onSubmit: submitVehicleFormHandler,
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
                  value={vehicleData?.id}
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
                  label="Year"
                  id="year"
                  name="year"
                  autoFocus
                  defaultValue={vehicleData?.year}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Make"
                  id="make"
                  name="make"
                  defaultValue={vehicleData?.make}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Model"
                  id="model"
                  name="model"
                  defaultValue={vehicleData?.model}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Vin"
                  id="vin"
                  name="vin"
                  type="number"
                  defaultValue={vehicleData?.vin}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Usage"
                  id="usage"
                  name="usage"
                  defaultValue={vehicleData?.usage}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Primary Use"
                  id="primaryUse"
                  name="primaryUse"
                  defaultValue={vehicleData?.primaryUse}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Annual Mileage"
                  id="annualMileage"
                  name="annualMileage"
                  type="number"
                  defaultValue={vehicleData?.annualMileage}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Ownership"
                  id="ownership"
                  name="ownership"
                  defaultValue={vehicleData?.ownership}
                />
                <Typography
                  variant="subtitle1"
                  display="block"
                  sx={{ marginTop: 1 }}
                >
                  Garaging Address
                </Typography>
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="street"
                  label="Street"
                  name="street"
                  defaultValue={vehicleData?.garagingAddress?.street ?? ""}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="city"
                  label="City"
                  name="city"
                  defaultValue={vehicleData?.garagingAddress?.city ?? ""}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="state"
                  label="State"
                  name="state"
                  defaultValue={vehicleData?.garagingAddress?.state ?? ""}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  id="zip"
                  label="Zip"
                  name="zip"
                  type="number"
                  defaultValue={vehicleData?.garagingAddress?.zip ?? ""}
                />
              </Box>
            </Container>
          </DialogContent>
          <DialogActions>
            <Button
              variant="outlined"
              onClick={() => {
                setVehicleData(defaultVehicleData);
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
      {vehicleId > 0 && coverageData?.id >= 0 && (
        <Dialog
          open={true}
          maxWidth="sm"
          fullWidth={true}
          PaperProps={{
            component: "form",
            onSubmit: submitCoverageFormHandler,
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
                  {titleCoverage}
                </Typography>
                <TextField
                  sx={{ visibility: "hidden" }}
                  type="hidden"
                  name="id"
                  value={coverageData?.id}
                />
                <TextField
                  sx={{ visibility: "hidden" }}
                  type="hidden"
                  name="vehicleId"
                  value={vehicleId}
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
                  label="Type"
                  id="type"
                  name="type"
                  autoFocus
                  defaultValue={coverageData?.type}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Limit"
                  id="limit"
                  name="limit"
                  defaultValue={coverageData?.limit}
                />
                <TextField
                  margin="normal"
                  required
                  fullWidth
                  label="Deductible"
                  id="deductible"
                  name="deductible"
                  defaultValue={coverageData?.deductible}
                />
              </Box>
            </Container>
          </DialogContent>
          <DialogActions>
            <Button
              variant="outlined"
              onClick={() => {
                setCoverageData(defaultCoverageData);
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
