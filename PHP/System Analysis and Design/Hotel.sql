CREATE DATABASE Hotel;
USE Hotel;

CREATE TABLE HotelUser (
    email VARCHAR(255) PRIMARY KEY,
    fullName VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);

CREATE TABLE Customer (
    customerID VARCHAR(50) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    nationalID VARCHAR(50) UNIQUE,
    phoneNumber VARCHAR(50),
    FOREIGN KEY (email) REFERENCES HotelUser(email) ON DELETE CASCADE
);

CREATE TABLE Receptionist (
    staffID VARCHAR(50) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    FOREIGN KEY (email) REFERENCES HotelUser(email) ON DELETE CASCADE
);

CREATE TABLE Room (
    roomNumber VARCHAR(50) PRIMARY KEY,
    bedType VARCHAR(100),
    pricePerNight FLOAT NOT NULL,
    maxGuests INT NOT NULL,
    amenities TEXT,
    rules TEXT,
    status VARCHAR(50) NOT NULL,
    standardCheckOutTime TIME,
    lateCheckOutFeePerHour FLOAT,
    earlyCheckOutRefundRate FLOAT,
    averageRating FLOAT
);

CREATE TABLE Booking (
    bookingID VARCHAR(50) PRIMARY KEY,
    customerID VARCHAR(50) NOT NULL,
    roomNumber VARCHAR(50) NOT NULL,
    bookingDate DATETIME NOT NULL,
    checkInDate DATE NOT NULL,
    checkOutDate DATE NOT NULL,
    checkInDeadline DATETIME,
    actualCheckOutDate DATETIME,
    numberOfGuests INT NOT NULL,
    serviceCharge FLOAT DEFAULT 0,
    lateFee FLOAT DEFAULT 0,
    earlyCheckOutDeduction FLOAT DEFAULT 0,
    totalAmount FLOAT NOT NULL,
    isPaid BOOLEAN DEFAULT FALSE,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (customerID) REFERENCES Customer(customerID),
    FOREIGN KEY (roomNumber) REFERENCES Room(roomNumber)
);

CREATE TABLE Review (
    reviewID VARCHAR(50) PRIMARY KEY,
    bookingID VARCHAR(50) UNIQUE NOT NULL,
    customerID VARCHAR(50) NOT NULL,
    roomQualityRating INT CHECK (roomQualityRating BETWEEN 1 AND 5),
    staffAttitudeRating INT CHECK (staffAttitudeRating BETWEEN 1 AND 5),
    generalAmenitiesRating INT CHECK (generalAmenitiesRating BETWEEN 1 AND 5),
    comment TEXT,
    submittedAt DATETIME NOT NULL,
    FOREIGN KEY (bookingID) REFERENCES Booking(bookingID) ON DELETE CASCADE,
    FOREIGN KEY (customerID) REFERENCES Customer(customerID)
);

CREATE TABLE HotelService (
    serviceID VARCHAR(50) PRIMARY KEY,
    serviceName VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    category VARCHAR(50) NOT NULL
);

CREATE TABLE ServiceRequest (
    requestID VARCHAR(50) PRIMARY KEY,
    bookingID VARCHAR(50) NOT NULL,
    requestDate DATETIME NOT NULL,
    totalCost FLOAT DEFAULT 0,
    isConfirmed BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (bookingID) REFERENCES Booking(bookingID) ON DELETE CASCADE
);

CREATE TABLE ServiceRequestItem (
    requestID VARCHAR(50) NOT NULL,
    serviceID VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unitPrice FLOAT NOT NULL,
    PRIMARY KEY (requestID, serviceID),
    FOREIGN KEY (requestID) REFERENCES ServiceRequest(requestID) ON DELETE CASCADE,
    FOREIGN KEY (serviceID) REFERENCES HotelService(serviceID)
);

INSERT INTO HotelUser (email, fullName, password, role) VALUES
('nguyenvanan@gmail.com', 'Nguyen Van An', 'hashed_password_123', 'Customer'),
('tranthibinh@gmail.com', 'Tran Thi Binh', 'hashed_password_456', 'Customer'),
('leminhcuong@gmail.com', 'Le Minh Cuong', 'hashed_password_789', 'Customer'),
('receptionist1@hotel.com', 'Pham Thi Thu', 'staff_pass_111', 'Receptionist'),
('receptionist2@hotel.com', 'Hoang Van Nam', 'staff_pass_222', 'Receptionist');

INSERT INTO Customer (customerID, email, nationalID, phoneNumber) VALUES
('CUST001', 'nguyenvanan@gmail.com', '079123456789', '0901112233'),
('CUST002', 'tranthibinh@gmail.com', '079987654321', '0904445566'),
('CUST003', 'leminhcuong@gmail.com', '079555444333', '0907778899');

INSERT INTO Receptionist (staffID, email) VALUES
('STAFF01', 'receptionist1@hotel.com'),
('STAFF02', 'receptionist2@hotel.com');

INSERT INTO Room (roomNumber, bedType, pricePerNight, maxGuests, amenities, rules, status, standardCheckOutTime, lateCheckOutFeePerHour, earlyCheckOutRefundRate, averageRating) VALUES
('101', 'Single Bed', 500000, 1, 'Free Wi-Fi, AC, TV, Mini bar', 'No smoking, No pets', 'Available', '12:00:00', 50000, 0.5, 4.5),
('102', 'Double Bed', 800000, 2, 'Free Wi-Fi, AC, Bathtub, Ocean view', 'No smoking', 'Available', '12:00:00', 80000, 0.5, 4.8),
('201', 'Queen Bed', 1200000, 2, 'Free Wi-Fi, AC, Balcony, Kitchenette', 'No smoking, No parties', 'Available', '12:00:00', 100000, 0.5, 0),
('202', 'King Suite', 2000000, 4, 'Living room, Jacuzzi, Free Breakfast', 'No smoking', 'Available', '12:00:00', 150000, 0.5, 0);

INSERT INTO Booking (bookingID, customerID, roomNumber, bookingDate, checkInDate, checkOutDate, checkInDeadline, actualCheckOutDate, numberOfGuests, serviceCharge, lateFee, earlyCheckOutDeduction, totalAmount, isPaid, status) VALUES
('BK001', 'CUST001', '101', '2026-10-25 10:00:00', '2026-11-01', '2026-11-03', '2026-11-01 20:00:00', NULL, 1, 0, 0, 0, 1000000, FALSE, 'AwaitingCheckIn'),
('BK002', 'CUST002', '102', '2026-10-26 14:00:00', '2026-10-28', '2026-10-30', '2026-10-28 20:00:00', '2026-10-30 11:00:00', 2, 150000, 0, 0, 1750000, TRUE, 'CheckedOut'),
('BK003', 'CUST003', '201', '2026-10-29 09:00:00', '2026-10-30', '2026-11-02', '2026-10-30 20:00:00', NULL, 2, 0, 0, 0, 3600000, FALSE, 'CheckedIn');

INSERT INTO Review (reviewID, bookingID, customerID, roomQualityRating, staffAttitudeRating, generalAmenitiesRating, comment, submittedAt) VALUES
('REV001', 'BK002', 'CUST002', 5, 5, 4, 'Phòng sạch sẽ, view biển tuyệt đẹp, nhân viên rất thân thiện.', '2026-10-30 11:30:00');

INSERT INTO HotelService (serviceID, serviceName, price, category) VALUES
('SRV001', 'Mineral Water', 20000, 'Beverage'),
('SRV002', 'Coca Cola', 30000, 'Beverage'),
('SRV003', 'Club Sandwich', 100000, 'Food'),
('SRV004', 'Laundry Service', 50000, 'Other'),
('SRV005', 'Spa Massage', 300000, 'Other');

INSERT INTO ServiceRequest (requestID, bookingID, requestDate, totalCost, isConfirmed) VALUES
('REQ001', 'BK002', '2026-10-29 19:00:00', 150000, TRUE);

INSERT INTO ServiceRequestItem (requestID, serviceID, quantity, unitPrice) VALUES
('REQ001', 'SRV001', 2, 20000),
('REQ001', 'SRV003', 1, 100000);