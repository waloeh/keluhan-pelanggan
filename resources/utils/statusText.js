export const statusText = (code) => {
  switch (code) {
    case '0': return 'Received'
    case '1': return 'Process'
    case '2': return 'Done'
    default: return 'Unknown'
  }
}