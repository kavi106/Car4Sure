import * as React from "react";
import { DataGrid } from "@mui/x-data-grid";
import Box from "@mui/material/Box";
import Stack from "@mui/material/Stack";
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
import PictureAsPdfOutlinedIcon from '@mui/icons-material/PictureAsPdfOutlined';
import { callApi } from "../callApi";

//https://mui.com/x/react-data-grid/layout/
//https://stackblitz.com/edit/mui-datagrid-button?file=src%2FApp.js
export default function PolicyList(props) {
  const [rows, setRows] = React.useState([]);
  // const [clickedRow, setClickedRow] = React.useState();
  // const onButtonClick = (e, row) => {
  //   e.stopPropagation();
  //   setClickedRow(row);
  // };

  React.useEffect(() => {
    callApi("GET", `/api/policies`, {}, props.token).then((response) => {
      if (response[0]?.id) {
        setRows(response);
      } else if (response.code) {
        props.onLogout();
      }
    });
  }, []);

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
      renderCell: () => {
        return (
          <Button variant="outlined" startIcon={<Person2OutlinedIcon />}>
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
      renderCell: () => {
        return (
          <Button
            variant="outlined"
            startIcon={<DirectionsCarFilledOutlinedIcon />}
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
            onClick={(e) => onButtonClick(e, params.row)}
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
            onClick={(e) => onButtonClick(e, params.row)}
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
            onClick={(e) => onButtonClick(e, params.row)}
          >
            <EditOutlinedIcon />
          </IconButton>
        );
      },
    },
  ];

  // const rows = [
  //   { id: 1, lastName: "Snow", firstName: "Jon", age: 35 },
  //   { id: 2, lastName: "Lannister", firstName: "Cersei", age: 42 },
  //   { id: 3, lastName: "Lannister", firstName: "Jaime", age: 45 },
  //   { id: 4, lastName: "Stark", firstName: "Arya", age: 16 },
  //   { id: 5, lastName: "Targaryen", firstName: "Daenerys", age: null },
  //   { id: 6, lastName: "Melisandre", firstName: null, age: 150 },
  //   { id: 7, lastName: "Clifford", firstName: "Ferrara", age: 44 },
  //   { id: 8, lastName: "Frances", firstName: "Rossini", age: 36 },
  //   { id: 9, lastName: "Roxie", firstName: "Harvey", age: 65 },
  // ];
  return (
    <Container sx={{ marginTop: 2 }} maxWidth="xl">
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
      {/* clickedRow: {clickedRow ? `${clickedRow.firstName}` : null} */}
    </Container>
  );
}
