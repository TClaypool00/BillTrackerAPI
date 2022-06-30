-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2022 at 05:42 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

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
CREATE DATABASE IF NOT EXISTS `billtracker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `billtracker`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `delMisc` (IN `id` INT(11))   DELETE FROM miscellaneous where MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(15,2), IN `company_id` INT(11), IN `date_due` DATE)   BEGIN
	DECLARE billId INT;

    INSERT INTO bills (BillName, AmountDue, CompanyId)
    VALUES (bill_name, amount_due, company_id);

    SET billId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (billId, 1, date_due);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insCompany` (IN `company_name` VARCHAR(255), IN `user_id` INT(11), IN `type_id` INT(11))   INSERT INTO companies (CompanyName, UserId, TypeId)
VALUES (company_name, user_id, type_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insLoan` (IN `loan_name` VARCHAR(255), IN `monthly_amt_due` DECIMAL(11,2), IN `total_loan_amt` DECIMAL(11,2), IN `remaining_amt` DECIMAL(11,2), IN `company_id` INT(11), IN `date_due` DATE)   BEGIN
	DECLARE loanId INT;

	INSERT INTO loans (LoanName, MonthlyAmountDue, TotalAmountDue, RemainingAmount, CompanyId)
	VALUES (loan_name, monthly_amt_due, total_loan_amt, remaining_amt, company_id);
    
    SET loanId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (loanId, 2, date_due);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11))   BEGIN
	DECLARE miscId INT;

	INSERT INTO miscellaneous (Name, Amount, CompanyId)
	VALUES (name, amount, company_id);
    
    SET miscId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId)
    VALUES (miscId, 4);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insPaymentHistory` (IN `expense_id` INT(11), IN `type_id` INT(11))   INSERT INTO paymenthistory (ExpenseId, TypeId)
VALUES (expense_id, type_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `company_id` INT)   BEGIN
	DECLARE subId INT;

	INSERT INTO subscriptions (Name, AmountDue, CompanyId)
    VALUES (name, amount_due, company_id);
    
    SET subId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (subId, 3, due_date);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255), IN `phone_num` VARCHAR(10))   BEGIN
	DECLARE user_id INT;
	INSERT INTO users (FirstName, LastName, Email, Password, IsAdmin, PhoneNumber)
    VALUES (first_name, last_name, email, password, FALSE, phone_num);
    
    SET user_id = LAST_INSERT_ID();
    
    INSERT INTO userprofile (UserId)
    VALUES (user_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selCompanyDropDown` (IN `type_id` INT(11), IN `user_id` INT(11))   SELECT c.CompanyId, c.CompanyName FROM companies c
WHERE c.TypeId = type_id AND c.UserId = user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `is_recurring` BOOLEAN, IN `is_active` BOOLEAN, IN `end_date` DATE, IN `bill_id` INT(11))   UPDATE bills
SET BillName = bill_name, AmountDue = amount_due, IsRecurring = is_recurring, IsActive = is_active, EndDate = end_date
WHERE BillId = bill_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updCompanyName` (IN `company_name` VARCHAR(255), IN `company_id` INT(11))   UPDATE companies
SET CompanyName = company_name
WHERE CompanyId = company_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updLoan` (IN `loan_name` VARCHAR(255), IN `is_active` BOOLEAN, IN `monthly_amt_due` DECIMAL(11,2), IN `total_amt_due` DECIMAL(11,2), IN `remaining_amt_due` DECIMAL(11,2), IN `loan_id` INT(11))   UPDATE loans
SET IsActive = is_active, MonthlyAmountDue = monthly_amt_due, TotalAmountDue = total_amt_due, RemainingAmount = remaining_amt_due
WHERE LoanId = loan_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11), IN `id` INT(11))   UPDATE miscellaneous
SET Name = name, Amount = amount, CompanyId = company_id
WHERE MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updPayExpense` (IN `expense_id` INT(11), IN `amount` DECIMAL(11,2), IN `type_id` INT(11))   BEGIN
    UPDATE paymenthistory
    SET IsPaid = TRUE, DatePaid = NOW(), Amount = amount
    WHERE ExpenseId = expense_id AND TypeId = type_id
    AND MONTH(DateDue) = MONTH(NOW());

    IF type_id = 2 THEN
      UPDATE loans
        SET RemainingAmount = RemainingAmount - amount
        WHERE LoanId = expense_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `is_active` BOOLEAN, IN `company_id` INT, IN `id` INT)   UPDATE subscriptions
SET Name = name, AmountDue = amount_due, DateDue = due_date, IsActive = is_active, CompanyId = company_id
WHERE SubscriptionId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `phone_num` VARCHAR(10), IN `user_id` INT(11))   UPDATE users
SET FirstName = first_name, LastName = last_name, Email = email, PhoneNumber = phone_num
WHERE UserId = user_id$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `BillId` int(11) NOT NULL,
  `BillName` varchar(255) NOT NULL,
  `AmountDue` decimal(15,2) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
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
  `IsLate` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `SubscriptionId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `AmountDue` decimal(11,2) NOT NULL,
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
,`IsActive` tinyint(1)
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
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
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
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
,`IsActive` tinyint(1)
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwbills`  AS SELECT `b`.`BillId` AS `BillId`, `b`.`BillName` AS `BillName`, `b`.`AmountDue` AS `AmountDue`, `b`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `b`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`bills` `b` join `companies` `c` on(`c`.`CompanyId` = `b`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `b`.`BillId` and `h`.`TypeId` = 1 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;

-- --------------------------------------------------------

--
-- Structure for view `vwcompanies`
--
DROP TABLE IF EXISTS `vwcompanies`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwcompanies`  AS SELECT `c`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`TypeId` AS `TypeId`, `c`.`UserId` AS `UserId`, `c`.`IsActive` AS `IsActive`, `t`.`TypeName` AS `TypeName`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`companies` `c` join `types` `t` on(`t`.`TypeId` = `c`.`TypeId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`))  ;

-- --------------------------------------------------------

--
-- Structure for view `vwloans`
--
DROP TABLE IF EXISTS `vwloans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwloans`  AS SELECT `l`.`LoanId` AS `LoanId`, `l`.`LoanName` AS `LoanName`, `l`.`IsActive` AS `IsActive`, `l`.`MonthlyAmountDue` AS `MonthlyAmountDue`, `l`.`TotalAmountDue` AS `TotalAmountDue`, `l`.`RemainingAmount` AS `RemainingAmount`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `l`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`loans` `l` join `companies` `c` on(`c`.`CompanyId` = `l`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `l`.`LoanId` and `h`.`TypeId` = 2 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;

-- --------------------------------------------------------

--
-- Structure for view `vwmiscellaneous`
--
DROP TABLE IF EXISTS `vwmiscellaneous`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwmiscellaneous`  AS SELECT `m`.`MiscellaneousId` AS `MiscellaneousId`, `m`.`Name` AS `Name`, `m`.`Amount` AS `Amount`, `m`.`DateAdded` AS `DateAdded`, `m`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`miscellaneous` `m` join `companies` `c` on(`c`.`CompanyId` = `m`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`))  ;

-- --------------------------------------------------------

--
-- Structure for view `vwusers`
--
DROP TABLE IF EXISTS `vwusers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwusers`  AS SELECT `users`.`UserId` AS `UserId`, `users`.`FirstName` AS `FirstName`, `users`.`LastName` AS `LastName`, `users`.`Email` AS `Email`, `users`.`PhoneNumber` AS `PhoneNumber`, `users`.`Password` AS `Password`, `users`.`IsAdmin` AS `IsAdmin` FROM `users`;

-- --------------------------------------------------------

--
-- Structure for view `wvsubscriptions`
--
DROP TABLE IF EXISTS `wvsubscriptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wvsubscriptions`  AS SELECT `s`.`SubscriptionId` AS `SubscriptionId`, `s`.`Name` AS `Name`, `s`.`AmountDue` AS `AmountDue`, `s`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `s`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`subscriptions` `s` join `companies` `c` on(`c`.`CompanyId` = `s`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `s`.`SubscriptionId` and `h`.`TypeId` = 3 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;

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
