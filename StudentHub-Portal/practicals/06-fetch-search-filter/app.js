'use strict';

const config = window.studentHubDatasetConfig;
const datasetSelect = document.getElementById('dataset-select');
const searchInput = document.getElementById('record-search');
const filterSelect = document.getElementById('record-filter');
const sortFieldSelect = document.getElementById('sort-field');
const sortDirectionSelect = document.getElementById('sort-direction');
const statusMessage = document.getElementById('load-status');
const resultCount = document.getElementById('result-count');
const tableHead = document.getElementById('record-head');
const tableBody = document.getElementById('record-rows');
const pageStatus = document.getElementById('page-status');
const previousButton = document.getElementById('previous-page');
const nextButton = document.getElementById('next-page');
const pageSize = 5;

let records = [];
let currentPage = 1;
let requestController;

function setLoadingState(dataset) {
  statusMessage.classList.remove('error-message');
  statusMessage.textContent = `Loading ${config[dataset].label.toLocaleLowerCase()}…`;
  tableBody.replaceChildren();
  const row = document.createElement('tr');
  const cell = document.createElement('td');
  cell.textContent = 'Loading…';
  cell.colSpan = config[dataset].columns.length;
  row.append(cell);
  tableBody.append(row);
}

function makeOption(label, value) {
  const option = document.createElement('option');
  option.textContent = label;
  option.value = value;
  return option;
}

function configureControls(dataset) {
  const settings = config[dataset];
  filterSelect.replaceChildren(makeOption('All', ''));
  [...new Set(records.map((record) => record[settings.filterKey]))]
    .sort((left, right) => left.localeCompare(right))
    .forEach((value) => filterSelect.append(makeOption(value, value)));

  sortFieldSelect.replaceChildren();
  settings.sortKeys.forEach(([key, label]) => {
    sortFieldSelect.append(makeOption(label, key));
  });

  const headerRow = document.createElement('tr');
  settings.columns.forEach(([, label]) => {
    const heading = document.createElement('th');
    heading.scope = 'col';
    heading.textContent = label;
    headerRow.append(heading);
  });
  tableHead.replaceChildren(headerRow);
}

function getVisibleRecords(dataset) {
  const settings = config[dataset];
  const term = searchInput.value.trim().toLocaleLowerCase();
  const selectedFilter = filterSelect.value;
  const sortKey = sortFieldSelect.value;
  const direction = sortDirectionSelect.value === 'desc' ? -1 : 1;

  return records
    .filter((record) => {
      const searchableText = settings.searchKeys
        .map((key) => record[key])
        .join(' ')
        .toLocaleLowerCase();
      return searchableText.includes(term)
        && (selectedFilter === '' || record[settings.filterKey] === selectedFilter);
    })
    .sort((left, right) => left[sortKey].localeCompare(right[sortKey]) * direction);
}

function renderRecords() {
  if (records.length === 0) {
    return;
  }

  const dataset = datasetSelect.value;
  const settings = config[dataset];
  const visibleRecords = getVisibleRecords(dataset);
  const pageCount = Math.max(1, Math.ceil(visibleRecords.length / pageSize));
  currentPage = Math.min(currentPage, pageCount);
  const firstRecord = (currentPage - 1) * pageSize;
  const pageRecords = visibleRecords.slice(firstRecord, firstRecord + pageSize);

  tableBody.replaceChildren();
  if (pageRecords.length === 0) {
    const row = document.createElement('tr');
    const cell = document.createElement('td');
    cell.colSpan = settings.columns.length;
    cell.textContent = 'No records match these options.';
    row.append(cell);
    tableBody.append(row);
  } else {
    pageRecords.forEach((record) => {
      const row = document.createElement('tr');
      settings.columns.forEach(([key]) => {
        const cell = document.createElement('td');
        cell.textContent = record[key];
        row.append(cell);
      });
      tableBody.append(row);
    });
  }

  resultCount.textContent = `${settings.label}: ${visibleRecords.length} record(s)`;
  pageStatus.textContent = `Page ${currentPage} of ${pageCount}`;
  previousButton.disabled = currentPage <= 1;
  nextButton.disabled = currentPage >= pageCount;
}

async function loadDataset() {
  const dataset = datasetSelect.value;
  if (requestController) {
    requestController.abort();
  }
  requestController = new AbortController();
  currentPage = 1;
  records = [];
  setLoadingState(dataset);

  try {
    const response = await fetch(config[dataset].file, {
      headers: { Accept: 'application/json' },
      signal: requestController.signal
    });
    if (!response.ok) {
      throw new Error(`Request failed with HTTP ${response.status}.`);
    }

    const data = await response.json();
    const requiredKeys = [
      ...new Set([
        ...config[dataset].searchKeys,
        config[dataset].filterKey,
        ...config[dataset].sortKeys.map(([key]) => key),
        ...config[dataset].columns.map(([key]) => key)
      ])
    ];
    if (
      !Array.isArray(data)
      || data.length < 15
      || !data.every((record) => record && requiredKeys.every((key) => typeof record[key] === 'string'))
    ) {
      throw new Error('The JSON data must contain at least 15 valid records.');
    }

    records = data;
    configureControls(dataset);
    renderRecords();
    statusMessage.classList.remove('error-message');
    statusMessage.textContent = `${settingsLabel(dataset)} loaded successfully.`;
  } catch (error) {
    if (error.name === 'AbortError') {
      return;
    }
    console.error(`Unable to load the Practical 6 ${dataset} JSON data.`, error);
    statusMessage.textContent = `${settingsLabel(dataset)} could not be loaded. Start the PHP/HTTP server and try again.`;
    statusMessage.classList.add('error-message');
    records = [];
    tableBody.replaceChildren();
    const row = document.createElement('tr');
    const cell = document.createElement('td');
    cell.colSpan = config[dataset].columns.length;
    cell.textContent = 'Data is unavailable.';
    row.append(cell);
    tableBody.append(row);
    resultCount.textContent = `${config[dataset].label}: unavailable`;
    pageStatus.textContent = 'Page 1 of 1';
    previousButton.disabled = true;
    nextButton.disabled = true;
  }
}

function settingsLabel(dataset) {
  return config[dataset].label;
}

searchInput.addEventListener('input', () => {
  currentPage = 1;
  renderRecords();
});
filterSelect.addEventListener('change', () => {
  currentPage = 1;
  renderRecords();
});
sortFieldSelect.addEventListener('change', renderRecords);
sortDirectionSelect.addEventListener('change', renderRecords);
datasetSelect.addEventListener('change', loadDataset);
previousButton.addEventListener('click', () => {
  currentPage -= 1;
  renderRecords();
});
nextButton.addEventListener('click', () => {
  currentPage += 1;
  renderRecords();
});

loadDataset();
