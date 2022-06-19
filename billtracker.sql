-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2022 at 03:53 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `billtracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `BillId` int(11) NOT NULL,
  `BillName` varchar(255) NOT NULL,
  `AmountDue` decimal(15,2) NOT NULL,
  `IsRecurring` tinyint(1) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `EndDate` date DEFAULT NULL,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `CompanyId` int(11) NOT NULL,
  `CompanyName` varchar(255) NOT NULL,
  `TypeId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `LoanId` int(11) NOT NULL,
  `LoanName` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `MonthlyAmountDue` decimal(11,2) NOT NULL,
  `TotalAmountDue` decimal(11,2) NOT NULL,
  `RemainingAmount` decimal(11,2) NOT NULL,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `miscellaneous`
--

CREATE TABLE `miscellaneous` (
  `MiscellaneousId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Amount` decimal(11,2) NOT NULL,
  `DateAdded` date NOT NULL DEFAULT current_timestamp(),
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `paymenthistory`
--

CREATE TABLE `paymenthistory` (
  `PaymentId` int(11) NOT NULL,
  `ExpenseId` int(11) NOT NULL,
  `TypeId` int(11) NOT NULL,
  `IsPaid` tinyint(1) NOT NULL DEFAULT 0,
  `DateDue` date NOT NULL,
  `DatePaid` date DEFAULT NULL,
  `IsLate` tinyint(1) NOT NULL DEFAULT 0,
  `Amount` decimal(11,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `SubscriptionId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `AmountDue` decimal(11,2) NOT NULL,
  `DateDue` date NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `TypeId` int(11) NOT NULL,
  `TypeName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`TypeId`, `TypeName`) VALUES
(1, 'Bill'),
(2, 'Loan'),
(3, 'Subscriptions'),
(4, 'Miscellaneous');

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

CREATE TABLE `userprofile` (
  `ProfileId` int(11) NOT NULL,
  `MonthlySalary` decimal(11,2) NOT NULL DEFAULT 0.00,
  `Budget` decimal(11,2) NOT NULL DEFAULT 0.00,
  `Savings` decimal(11,2) NOT NULL DEFAULT 0.00,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(10) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwbills`
-- (See below for the actual view)
--
CREATE TABLE `vwbills` (
`BillId` int(11)
,`BillName` varchar(255)
,`AmountDue` decimal(15,2)
,`IsRecurring` tinyint(1)
,`IsActive` tinyint(1)
,`EndDate` date
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwcompanies`
-- (See below for the actual view)
--
CREATE TABLE `vwcompanies` (
`CompanyId` int(11)
,`CompanyName` varchar(255)
,`TypeId` int(11)
,`UserId` int(11)
,`IsActive` tinyint(1)
,`TypeName` varchar(255)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwloans`
-- (See below for the actual view)
--
CREATE TABLE `vwloans` (
`LoanId` int(11)
,`LoanName` varchar(255)
,`IsActive` tinyint(1)
,`MonthlyAmountDue` decimal(11,2)
,`TotalAmountDue` decimal(11,2)
,`RemainingAmount` decimal(11,2)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwmiscellaneous`
-- (See below for the actual view)
--
CREATE TABLE `vwmiscellaneous` (
`MiscellaneousId` int(11)
,`Name` varchar(255)
,`Amount` decimal(11,2)
,`DateAdded` date
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwusers`
-- (See below for the actual view)
--
CREATE TABLE `vwusers` (
`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
,`Email` varchar(255)
,`PhoneNumber` varchar(10)
,`Password` varchar(255)
,`IsAdmin` tinyint(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `wvsubscriptions`
-- (See below for the actual view)
--
CREATE TABLE `wvsubscriptions` (
`SubscriptionId` int(11)
,`Name` varchar(255)
,`AmountDue` decimal(11,2)
,`DateDue` date
,`IsActive` tinyint(1)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `vwbills`
--
DROP TABLE IF EXISTS `vwbills`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwbills`  AS SELECT `b`.`BillId` AS `BillId`, `b`.`BillName` AS `BillName`, `b`.`AmountDue` AS `AmountDue`, `b`.`IsRecurring` AS `IsRecurring`, `b`.`IsActive` AS `IsActive`, `b`.`EndDate` AS `EndDate`, `b`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`bills` `b` join `companies` `c` on(`c`.`CompanyId` = `b`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vwcompanies`
--
DROP TABLE IF EXISTS `vwcompanies`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwcompanies`  AS SELECT `c`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`TypeId` AS `TypeId`, `c`.`UserId` AS `UserId`, `c`.`IsActive` AS `IsActive`, `t`.`TypeName` AS `TypeName`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`companies` `c` join `types` `t` on(`t`.`TypeId` = `c`.`TypeId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vwloans`
--
DROP TABLE IF EXISTS `vwloans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwloans`  AS SELECT `l`.`LoanId` AS `LoanId`, `l`.`LoanName` AS `LoanName`, `l`.`IsActive` AS `IsActive`, `l`.`MonthlyAmountDue` AS `MonthlyAmountDue`, `l`.`TotalAmountDue` AS `TotalAmountDue`, `l`.`RemainingAmount` AS `RemainingAmount`, `l`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`loans` `l` join `companies` `c` on(`c`.`CompanyId` = `l`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vwmiscellaneous`
--
DROP TABLE IF EXISTS `vwmiscellaneous`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwmiscellaneous`  AS SELECT `m`.`MiscellaneousId` AS `MiscellaneousId`, `m`.`Name` AS `Name`, `m`.`Amount` AS `Amount`, `m`.`DateAdded` AS `DateAdded`, `m`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`miscellaneous` `m` join `companies` `c` on(`c`.`CompanyId` = `m`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vwusers`
--
DROP TABLE IF EXISTS `vwusers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwusers`  AS SELECT `users`.`UserId` AS `UserId`, `users`.`FirstName` AS `FirstName`, `users`.`LastName` AS `LastName`, `users`.`Email` AS `Email`, `users`.`PhoneNumber` AS `PhoneNumber`, `users`.`Password` AS `Password`, `users`.`IsAdmin` AS `IsAdmin` FROM `users` ;

-- --------------------------------------------------------

--
-- Structure for view `wvsubscriptions`
--
DROP TABLE IF EXISTS `wvsubscriptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wvsubscriptions`  AS SELECT `s`.`SubscriptionId` AS `SubscriptionId`, `s`.`Name` AS `Name`, `s`.`AmountDue` AS `AmountDue`, `s`.`DateDue` AS `DateDue`, `s`.`IsActive` AS `IsActive`, `s`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`subscriptions` `s` join `companies` `c` on(`s`.`CompanyId` = `c`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`BillId`),
  ADD KEY `fk_companies_bill` (`CompanyId`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`CompanyId`),
  ADD KEY `TypeId` (`TypeId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`LoanId`),
  ADD KEY `fk_loans_companies` (`CompanyId`);

--
-- Indexes for table `miscellaneous`
--
ALTER TABLE `miscellaneous`
  ADD PRIMARY KEY (`MiscellaneousId`),
  ADD KEY `CompanyId` (`CompanyId`);

--
-- Indexes for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  ADD PRIMARY KEY (`PaymentId`),
  ADD KEY `TypeId` (`TypeId`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`SubscriptionId`),
  ADD KEY `CompanyId` (`CompanyId`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`TypeId`);

--
-- Indexes for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`ProfileId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `BillId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `CompanyId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `LoanId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `miscellaneous`
--
ALTER TABLE `miscellaneous`
  MODIFY `MiscellaneousId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `SubscriptionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `TypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `userprofile`
--
ALTER TABLE `userprofile`
  MODIFY `ProfileId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `fk_companies_bill` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`TypeId`) REFERENCES `types` (`TypeId`),
  ADD CONSTRAINT `companies_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_companies` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

--
-- Constraints for table `miscellaneous`
--
ALTER TABLE `miscellaneous`
  ADD CONSTRAINT `miscellaneous_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

--
-- Constraints for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  ADD CONSTRAINT `paymenthistory_ibfk_1` FOREIGN KEY (`TypeId`) REFERENCES `types` (`TypeId`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

--
-- Constraints for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
