import * as React from "react";
import { DataGrid } from "@mui/x-data-grid";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Grid from "@mui/material/Grid";
import Container from "@mui/material/Container";
import AddOutlinedIcon from "@mui/icons-material/AddOutlined";
import Typography from "@mui/material/Typography";
import IconButton from "@mui/material/IconButton";
import DeleteForeverOutlinedIcon from "@mui/icons-material/DeleteForeverOutlined";
import EditOutlinedIcon from "@mui/icons-material/EditOutlined";
import Person2OutlinedIcon from "@mui/icons-material/Person2Outlined";
import DirectionsCarFilledOutlinedIcon from "@mui/icons-material/DirectionsCarFilledOutlined";
import PictureAsPdfOutlinedIcon from "@mui/icons-material/PictureAsPdfOutlined";
import { callApi } from "../callApi";
import Spinner from "./spinner";
import PolicyDialog from "./policy";
import AlertDialog from "./alert";
import DriversDialog from "./drivers";
import VehiclesDialog from "./vehicles";

const defaultAlert = { code: "", message: "" };

//https://mui.com/x/react-data-grid/layout/
//https://stackblitz.com/edit/mui-datagrid-button?file=src%2FApp.js
export default function PolicyList(props) {
  const [rows, setRows] = React.useState([]);
  const [spinner, setSpinner] = React.useState(true);
  const [refreshPage, setRefreshPage] = React.useState(null);
  const [policyData, setPolicyData] = React.useState(null);
  const [error, setError] = React.useState(defaultAlert);
  const [showDrivers, setShowDrivers] = React.useState(0);
  const [showVehicles, setShowVehicles] = React.useState(0);

  React.useEffect(() => {
    setSpinner(true);
    callApi("GET", `/api/policies`, {}, props.token).then((response) => {
      if (response[0]?.id) {
        setRows(response);
      } else if (response.code) {
        props.onLogout();
      }
      setSpinner(false);
    });
  }, [refreshPage]);

  const handleDeletePolicy = (id) => {
    setSpinner(true);
    callApi("DELETE", `/api/policy/${id}`, {}, props.token).then((response) => {
      if (response.code == 401) props.onLogout();
      if (response.code == 200) {
        setError(response);
        setRefreshPage(Math.random());
      }
      setSpinner(false);
    });
  };

  const driversDialogHandler = (id) => {
    setShowDrivers(id);
  };

  const vehiclesDialogHandler = (id) => {
    setShowVehicles(id);
  };

  const closeAlertHandler = () => {
    setError(defaultAlert);
  };

  const columns = [
    {
      field: "id",
      headerName: "Policy No",
      width: 110,
      valueFormatter: (params) => {
        return params.value.toString().padStart(10, "0");
      },
    },
    {
      field: "policyStatus",
      headerName: "Status",
      width: 70,
    },
    {
      field: "policyType",
      headerName: "Type",
      width: 50,
    },
    {
      field: "policyEffectiveDate",
      headerName: "Effective",
      width: 100,
      valueFormatter: (params) =>
        new Date(params.value.toString()).toLocaleDateString("en-CA"),
    },
    {
      field: "policyExpirationDate",
      headerName: "Expiration",
      width: 100,
      valueFormatter: (params) =>
        new Date(params.value.toString()).toLocaleDateString("en-CA"),
    },
    {
      field: "policyHolder",
      headerName: "Policy Holder",
      width: 210,
      valueGetter: (params) =>
        `${params.row.policyHolder.firstName || ""} ${
          params.row.policyHolder.lastName || ""
        }`,
    },
    {
      field: "address",
      headerName: "Address",
      width: 270,
      valueGetter: (params) =>
        `${params.row.policyHolder.address.street || ""}, ${
          params.row.policyHolder.address.city || ""
        }, ${params.row.policyHolder.address.state || ""}, ${
          params.row.policyHolder.address.zip || ""
        }`,
    },
    {
      field: "drivers",
      headerName: "Drivers",
      description: "Actions column.",
      sortable: false,
      width: 150,
      renderCell: (params) => {
        return (
          <Button
            variant="outlined"
            startIcon={<Person2OutlinedIcon />}
            onClick={() => {
              driversDialogHandler(params.row.id);
            }}
          >
            Drivers
          </Button>
        );
      },
    },
    {
      field: "vehicles",
      headerName: "Vehicles",
      description: "Actions column.",
      sortable: false,
      width: 150,
      renderCell: (params) => {
        return (
          <Button
            variant="outlined"
            startIcon={<DirectionsCarFilledOutlinedIcon />}
            onClick={() => {
              vehiclesDialogHandler(params.row.id);
            }}
          >
            Vehicles
          </Button>
        );
      },
    },
    {
      field: "pdfIcon",
      headerName: "Pdf",
      description: "Actions column.",
      sortable: false,
      width: 50,
      renderCell: (params) => {
        return (
          <IconButton
            size="small"
            color="primary"
          >
            <PictureAsPdfOutlinedIcon />
          </IconButton>
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
              handleDeletePolicy(params.row.id);
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
              setPolicyData(params.row);
            }}
          >
            <EditOutlinedIcon />
          </IconButton>
        );
      },
    },
  ];

  return (
    <Container sx={{ marginTop: 2 }} maxWidth="xl">
      {spinner && <Spinner />}
      {error.code !== "" && error.message !== "" && (
        <AlertDialog onClose={closeAlertHandler}>{error.message}</AlertDialog>
      )}
      {showDrivers > 0 && (
        <DriversDialog
          policy={showDrivers}
          token={props.token}
          onLogout={props.onLogout}
          onClose={setShowDrivers}
        />
      )}
      {showVehicles > 0 && (
        <VehiclesDialog
          policy={showVehicles}
          token={props.token}
          onLogout={props.onLogout}
          onClose={setShowVehicles}
        />
      )}
      {policyData?.id >= 0 && (
        <PolicyDialog
          policyData={policyData}
          onShowPolicy={setPolicyData}
          onRefreshPolicies={setRefreshPage}
          onLogout={props.onLogout}
          token={props.token}
        />
      )}
      <Typography component="h2" variant="body" align="center">
        List of policies
      </Typography>
      <Box alignItems="center">
        <Grid container>
          <Grid item xs></Grid>
          <Grid item>
            <Button
              disableRipple
              size="small"
              variant="contained"
              startIcon={<AddOutlinedIcon />}
              onClick={() => {
                // setShowPolicy(0);
                setPolicyData({ id: 0 });
              }}
            >
              Policy
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
  );
}
