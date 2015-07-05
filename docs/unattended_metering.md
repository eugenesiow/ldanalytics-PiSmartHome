##Unattended Metering
---

Diagnostic queries are about diving deeper into data. Unattended metering relates 2 sets of data spatially and temporally.
Motion data for various rooms in the house (living room, master bedroom, kitchen, etc.) and power meter data from devices in these rooms.
The temporal resolution is done down to hour and the spatial resolution by room.
The query finds the devices which have active power consumption when there is no motion in the room and lists them in a table for delving deeper in to diagnosing energy wastage.

###Input Variables
Name | Type | Example
--- | --- | ---
startDate | xsdDateTime | 2012-07-18T00:00:00
endDate | xsdDateTime |2012-07-20T00:00:00